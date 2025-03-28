@extends('layouts.app')

@section('title', 'Commission Summary for Dillon')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-body">
            <!-- Filter Form -->
            <form action="{{ route('divanj.commission.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="week" class="form-label fw-bold">Select Week:</label>
                        <input type="week" name="week" id="week" class="form-control" value="{{ $week ?? '' }}" required>
                    </div>
                <div class="col-md-4">
                    <label for="client_id" class="form-label fw-bold">Select Client:</label>
                    <select name="client_id" id="client_id" class="form-select" required>
                        <option value="">-- Select --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ (isset($clientId) && $clientId == $client->id) ? 'selected' : '' }}>
                                {{ $client->company }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row mt-4">
                    <div class="col text-center">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">Generate Report</button>
                    </div>
                </div>
        </div>
            </form>
    </div>

    <!-- Weekly Summary -->
    @if($weeklySummary && $salesData && count($salesData))
        <div class="alert alert-info mt-4">
            <strong>Total Sales Qty (All Agents):</strong> {{ $weeklySummary->total_qty }}
        </div>
    @endif

    <!-- Report Table -->
    @if($salesData && count($salesData))
        <div class="card mt-4">
            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Agent</th>
                            <th>Date Range</th>
                            <th>Target</th>
                            <th>Achieved</th>
                            <th>Weekly Comm</th>
                            <th>Weekend Sales ($)</th>
                            <th>Weekend Comm </th>
                            <th>Total Comm</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesData as $row)
                            @php
                                $target = 0; // Default. Could be pulled later.
                                $weekly_commission = 0;
                                $weekend_commission = $row->weekend_sales * 0.06;
                                $total_commission = $weekly_commission + $weekend_commission;
                                $total_per_agent = $target > 0 ? ($row->achieved * 100 / $target) : 0;
                            @endphp
                        <tr>
                            <td>
                                {{ $row->stage_name }}
                                <input type="hidden" class="employee_id" value="{{ $row->employee_id }}">
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($row->start_date)->format('M d') }} -
                                {{ \Carbon\Carbon::parse($row->end_date)->format('M d, Y') }}
                            </td>
                            <td>
                                <input type="number" class="form-control text-center target" value="{{ $target }}">
                            </td>
                            <td>{{ $row->achieved }}</td>
                            <td>
                                <input type="number" class="form-control text-center weekly_commission" value="{{ $weekly_commission }}">
                            </td>
                        <td>
                            <input type="number" step="0.01" class="form-control text-center weekend_sales" value="{{ number_format($row->weekend_sales, 2) }}">
                        </td>
                        <td>
                            <input type="number" step="0.01" class="form-control text-center weekend_commission" value="{{ number_format($weekend_commission, 2) }}">
                        </td>
                            <td>{{ number_format($total_commission, 2) }}</td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
$(document).on('blur', '.target, .achieved, .weekly_commission, .weekend_sales, .weekend_commission', function() {
    var row = $(this).closest('tr');
    var data = {
        employee_id: row.find('.employee_id').val(),
        target: row.find('.target').val(),
        achieved: row.find('.achieved').val(),
        weekly_commission: row.find('.weekly_commission').val(),
        weekend_sales: row.find('.weekend_sales').val(),
        weekend_commission: row.find('.weekend_commission').val(),
        _token: '{{ csrf_token() }}'
    };

    $.ajax({
        url: '{{ route("divanj.commission.update") }}',
        method: 'POST',
        data: data,
        success: function(response) {
            alert('Data updated successfully.');
        },
        error: function(xhr) {
            alert('Error updating data.');
        }
    });
});

    </script>
@endsection
