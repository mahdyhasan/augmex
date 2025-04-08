@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<style>
    .offcanvas {
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        height: 60vh; 
    }
    .offcanvas-header {
        background: #007bff;
        color: white;
    }
    .offcanvas-body {
        padding: 20px;
    }
    .summary-table {
        width: 100%;
        margin-bottom: 20px;
    }
    .summary-table th, .summary-table td {
        padding: 8px;
        border-bottom: 1px solid #e9ecef;
    }
    .summary-table th {
        background: #f8f9fa;
    }
    .toggle-summary-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Sales Report</h3>
                <a href="javascript:void(0);" class="btn btn-danger" data-bs-toggle="offcanvas" data-bs-target="#importSalesReport">
                    <i class="ti ti-square-rounded-plus me-2"></i> Import Sales
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Filter Form in one row -->
                <form action="{{ route('divanj.sales.report') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="employee_id" class="form-label">Employee</label>
                            <select name="employee_id" id="employee_id" class="form-select">
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->stage_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('divanj.sales.report') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>

                <hr>

                <!-- Group and aggregate sales by date and employee -->
                @php
                    // Group sales records by a unique key combining date and employee_id
                    $groupedSales = $sales->groupBy(function($sale) {
                        return $sale->date . '|' . $sale->employee->id;
                    });

                    // Calculate grand totals for the filtered records
                    $grandTotalQty = $sales->sum('quantity');
                    $grandTotalAmount = $sales->sum('total');
                @endphp

                <div class="table-responsive">
                    <table id="salesReportTable" class="table table-bordered mt-4">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Employee</th>
                                <th>Quantity</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groupedSales as $group)
                                @php
                                    $firstSale = $group->first();
                                    $groupDate = $firstSale->date;
                                    $employeeStageName = $firstSale->employee->stage_name ?? 'N/A';
                                    $groupQuantity = $group->sum('quantity');
                                    $groupTotal = $group->sum('total');
                                @endphp
                                <tr>
                                    <td>{{ $groupDate->format('Y-m-d (D)') }}</td>
                                    <td>{{ $employeeStageName }}</td>
                                    <td>{{ $groupQuantity }}</td>
                                    <td>{{ number_format($groupTotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No sales records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Grand Total:</th>
                                <th>{{ $grandTotalQty }}</th>
                                <th>{{ number_format($grandTotalAmount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>


<!-- Offcanvas for the Import Form -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="importSalesReport" aria-labelledby="importSalesReportLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title text-light" id="importSalesReportLabel">Import Sales</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('divanj.sales.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">Upload Excel File</label>
                <input type="file" class="form-control" name="file" id="file" required>
            </div>
            <button type="submit" class="btn btn-primary">Import</button>
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
        $('#salesReportTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "order": [[0, 'desc']] 
        });
    });
</script>
@endsection