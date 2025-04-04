@extends('layouts.app')

@section('title', 'Commission for Divanj')

@section('content')

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Commission Report - Divanj</h3>
            <a href="javascript:void(0);" class="btn btn-primary"
                   data-bs-toggle="offcanvas" data-bs-target="#generateCommissionModal">
                   <i class="ti ti-square-rounded-plus me-2"></i> Generate Commission
                </a>
            </div>
          <div class="card-body">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    <div class="table-responsive">
        <table id="commissionTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Week</th>
                    <th>Target</th>
                    <th>Achieved</th>
                    <th>Weekday Sales</th>
                    <th>Weekend Sales</th>
                    <th>Base Commission</th>
                    <th>Option A (Fixed)</th>
                    <th>Option B (Mixed)</th>
                    <th>Option C (Weekend)</th>
                    <th>Selected Option</th>
                    <th>Final Commission</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commissions as $commission)
                <tr>
                    <td>{{ $commission->employee->stage_name }}</td>
                    <td>{{ $commission->start_date }} to {{ $commission->end_date }}</td>
                    <td>{{ $commission->target }}</td>
                    <td>{{ $commission->achieved_qty }}</td>
                    <td>{{ $commission->weekday_sales_qty }} ({{ number_format($commission->weekday_sales_amount, 2) }})</td>
                    <td>{{ $commission->weekend_sales_qty }} ({{ number_format($commission->weekend_sales_amount, 2) }})</td>
                    <td>{{ number_format($commission->base_commission, 2) }}</td>
                    <td>{{ number_format($commission->option_a_amount, 2) }}</td>
                    <td>{{ number_format($commission->option_b_amount, 2) }}</td>
                    <td>{{ number_format($commission->option_c_amount, 2) }}</td>
                    <td>{{ ucfirst($commission->commission_type) }}</td>
                    <td class="font-weight-bold">{{ number_format($commission->commission_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Offcanvas Modal -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="generateCommissionModal" aria-labelledby="generateCommissionModalLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="generateCommissionModalLabel">Generate Commission</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Your form content here -->

        <form action="{{ route('divanj.commission.generate') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Generate</button>
        </form>
    </div>
</div>

@endsection



@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endsection

@section('js')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#commissionTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true
        });
    });
</script>
@endsection
