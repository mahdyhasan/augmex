@extends('layouts.app')

@section('title', 'Commission for Divanj')

@section('content')

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Commission Report - Divanj</h3>
                <div>
                    <a href="javascript:void(0);" class="btn btn-primary"
                       data-bs-toggle="offcanvas" data-bs-target="#generateCommissionModal">
                       <i class="ti ti-square-rounded-plus me-2"></i> Generate Commission
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row mb-3 g-3">
                    <div class="col-md-3">
                        <label for="employeeFilter" class="form-label">Employee</label>
                        <select id="employeeFilter" class="form-select">
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->stage_name }}">{{ $employee->stage_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="dateRangeFilter" class="form-label">Date Range</label>
                        <select id="dateRangeFilter" class="form-select">
                            <option value="">All Dates</option>
                            @foreach($commissions->unique('start_date') as $commission)
                                <option value="{{ $commission->start_date }} to {{ $commission->end_date }}">
                                    {{ $commission->start_date }} - {{ $commission->end_date }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="targetFilter" class="form-label">Target Achievement</label>
                        <select id="targetFilter" class="form-select">
                            <option value="">All</option>
                            <option value="achieved">Achieved Target</option>
                            <option value="not_achieved">Not Achieved</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button id="resetFilters" class="btn btn-outline-secondary w-100">
                            <i class="ti ti-refresh me-1"></i> Reset
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="commissionTable" class="table table-bordered table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Week</th>
                                <th class="text-end">Target</th>
                                <th class="text-end">Achieved</th>
                                <th class="text-end">Weekday Sales</th>
                                <th class="text-end">Weekend Sales</th>
                                <th class="text-end">Base Commission</th>
                                <th class="text-end">Option A</th>
                                <th class="text-end">Option B</th>
                                <th class="text-end">Option C</th>
                                <th>Selected</th>
                                <th class="text-end">Final Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commissions as $commission)
                            <tr>
                                <td>
                                    {{ $commission->employee->stage_name }}
                                </td>
                                <td>{{ $commission->start_date }} to {{ $commission->end_date }}</td>
                                <td class="text-end">{{ number_format($commission->target) }}</td>
                                <td class="text-end {{ $commission->achieved_qty >= $commission->target ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($commission->achieved_qty) }}
                                    @if($commission->achieved_qty >= $commission->target)
                                        <i class="ti ti-check text-success ms-1"></i>
                                    @else
                                        <i class="ti ti-x text-danger ms-1"></i>
                                    @endif
                                </td>
                                <td class="text-end">
                                    {{ number_format($commission->weekday_sales_qty) }}<br>
                                    <small class="text-muted">$ {{ number_format($commission->weekday_sales_amount, 2) }}</small>
                                </td>
                                <td class="text-end">
                                    {{ number_format($commission->weekend_sales_qty) }}<br>
                                    <small class="text-muted"> {{ number_format($commission->weekend_sales_amount, 2) }}</small>
                                </td>
                                <td class="text-end"> {{ number_format($commission->base_commission, 2) }}</td>
                                <td class="text-end"> {{ number_format($commission->option_a_amount, 2) }}</td>
                                <td class="text-end"> {{ number_format($commission->option_b_amount, 2) }}</td>
                                <td class="text-end"> {{ number_format($commission->option_c_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $commission->commission_type == 'a' ? 'primary' : 
                                        ($commission->commission_type == 'b' ? 'info' : 'success') 
                                    }}">
                                        {{ strtoupper($commission->commission_type) }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success"> {{ number_format($commission->commission_amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        @if($commissions->isNotEmpty())
                        <tfoot>
                            <tr class="table-active">
                                <th colspan="2">Totals</th>
                                <th class="text-end">{{ number_format($commissions->sum('target')) }}</th>
                                <th class="text-end">{{ number_format($commissions->sum('achieved_qty')) }}</th>
                                <th class="text-end">{{ number_format($commissions->sum('weekday_sales_qty')) }}</th>
                                <th class="text-end">{{ number_format($commissions->sum('weekend_sales_qty')) }}</th>
                                <th class="text-end"> {{ number_format($commissions->sum('base_commission'), 2) }}</th>
                                <th colspan="3"></th>
                                <th class="text-end"> {{ number_format($commissions->sum('commission_amount'), 2) }}</th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Commission Modal -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="generateCommissionModal">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">Generate Commission</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('divanj.commission.generate') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Date Range</label>
                <div class="input-daterange input-group">
                    <input type="date" class="form-control" name="start_date" required>
                    <span class="input-group-text">to</span>
                    <input type="date" class="form-control" name="end_date" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="ti ti-calculator me-2"></i> Calculate Commission
            </button>
        </form>
    </div>
</div>

@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<style>
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-initial {
        font-weight: 600;
        color: white;
    }
    .badge {
        font-size: 0.75em;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table td, .table th {
        vertical-align: middle;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }
</style>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#commissionTable').DataTable({
        dom: '<"top"<"row"<"col-md-6"B><"col-md-6"f>>>rt<"bottom"<"row"<"col-md-6"i><"col-md-6"p>>>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="ti ti-download me-1"></i> Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function(data, row, column, node) {
                            // Strip HTML tags and AUD symbol for Excel export
                            return data.replace(/<[^>]*>/g, '').replace('$', '');
                        }
                    }
                }
            },
            {
                extend: 'print',
                text: '<i class="ti ti-printer me-1"></i> Print',
                className: 'btn btn-info',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        responsive: true,
        initComplete: function() {
            // Add custom filtering for target achievement
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var target = parseFloat(data[2]) || 0;
                    var achieved = parseFloat(data[3]) || 0;
                    var filter = $('#targetFilter').val();
                    
                    if (filter === 'achieved') {
                        return achieved >= target;
                    } else if (filter === 'not_achieved') {
                        return achieved < target;
                    }
                    return true;
                }
            );
        }
    });

    // Employee filter
    $('#employeeFilter').on('change', function() {
        table.column(0).search(this.value).draw();
    });

    // Date range filter
    $('#dateRangeFilter').on('change', function() {
        table.column(1).search(this.value).draw();
    });

    // Target achievement filter
    $('#targetFilter').on('change', function() {
        table.draw();
    });

    // Reset filters
    $('#resetFilters').on('click', function() {
        $('#employeeFilter, #dateRangeFilter, #targetFilter').val('');
        table.search('').columns().search('').draw();
    });
});
</script>
@endsection