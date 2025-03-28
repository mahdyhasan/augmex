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
            <thead >
                <tr>
                    <th>ID</th>
                    <th>Employee</th>
                    <th>Date Range</th>
                    <th>Target</th>
                    <th>Achieved</th>
                    <th>Commission Type</th>
                    <th>Total Commission</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions as $commission)
                    <tr>
                        <td>{{ $commission->id }}</td>
                        <td>{{ $commission->employee->stage_name ?? 'N/A' }}</td>
                        <td>{{ $commission->start_date }} to {{ $commission->end_date }}</td>
                        <td>{{ $commission->target }}</td>
                        <td>{{ $commission->achieved_qty }}</td>
                        <td>{{ ucfirst($commission->commission_type) }}</td>
                        <td>${{ number_format($commission->commission_amount, 2) }}</td>
                        <td>
                            <a href="{{ route('divanj.commission.view', $commission->id) }}" class="btn btn-sm btn-info">View</a>
                            <a href="{{ route('divanj.commission.edit', $commission->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No commissions found.</td>
                    </tr>
                @endforelse
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
