@extends('layouts.app')

@section('title', 'Payroll Management')

@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 4px;
        padding: 4px 8px;
        border: 1px solid #ddd;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 4px;
        padding: 4px;
        border: 1px solid #ddd;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    .avatar img {
        width: 30px;
        height: 30px;
        object-fit: cover;
    }
    .text-success {
        color: #28a745 !important;
    }
    .text-danger {
        color: #dc3545 !important;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Payroll Management</h3>
                <div>
                    <button class="btn btn-light" onclick="openGeneratePayroll()">
                        <i class="fas fa-plus-circle me-1"></i> Generate Payroll
                    </button>
                    <button class="btn btn-light me-2" onclick="openSalarySheet()">
                        <i class="fas fa-file-invoice-dollar me-1"></i> Salary Sheet
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="payrollTable" class="table table-bordered table-hover table-striped w-100">
                        <thead >
                            <tr>
                                <th width="5%">#</th>
                                <th>Employee</th>
                                <th>Pay Period</th>
                                <th>Base Salary</th>
                                <th>Additions</th>
                                <th>Deductions</th>
                                <th>Net Salary</th>
                                <th>Status</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payrolls as $payroll)
                            <tr data-id="{{ $payroll->id }}" style="cursor: pointer;">
                                <td>{{ $payroll->id }}</td>
                                    <td>
                                        @if($payroll->employee)
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <strong>{{ $payroll->employee->user->name ?? 'N/A' }}</strong>
                                                    <div class="text-muted small">{{ $payroll->employee->position ?? '-' }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-danger">Employee Deleted</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('d M') }} - 
                                        {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('d M Y') }}
                                    </td>
                                    <td class="text-end">{{ number_format($payroll->base_salary, 2) }}</td>
                                    <td class="text-end">
                                        <div><small class="text-muted">Bonus:</small> {{ number_format($payroll->bonuses, 2) }}</div>
                                        <div><small class="text-muted">Commission:</small> {{ number_format($payroll->commission, 2) }}</div>
                                        <div><small class="text-muted">Transport:</small> {{ number_format($payroll->transport, 2) }}</div>
                                    </td>
                                    <td class="deduction-cell text-end" data-payroll-id="{{ $payroll->id }}" style="cursor: pointer; color: #dc3545;">
                                    {{ number_format($payroll->deductions, 2) }}
                                        @if($payroll->deductions > 0)
                                            <div class="text-muted small">Absence/Late</div>
                                        @endif
                                    </td>
                                    <td class="text-success text-end fw-bold">
                                        {{ number_format($payroll->net_salary, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill {{ $payroll->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($payroll->payment_status) }}
                                            @if($payroll->payment_date)
                                                <br><small>{{ \Carbon\Carbon::parse($payroll->payment_date)->format('d M Y') }}</small>
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            @if($payroll->payment_status == 'pending')
                                                <form action="{{ route('payrolls.markAsPaid', $payroll->id) }}" method="POST" class="me-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" title="Mark as Paid">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('payrolls.edit', $payroll->id) }}" class="btn btn-warning btn-sm me-1" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('payrolls.view', $payroll->id) }}" class="btn btn-info btn-sm" title="View Details" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Payroll Modal -->
<div class="modal fade" id="generatePayrollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Generate Payroll for All Employees</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="generatePayrollForm" action="{{ route('payrolls.generate.all') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Month</label>
                        <input type="month" name="month" class="form-control" 
                               value="{{ date('Y-m') }}" required>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Payroll will be generated from 25th of previous month to 24th of selected month
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="generatePayrollBtn">
                        <i class="fas fa-calculator me-1"></i> Generate Payroll
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Salary Sheet Modal -->
<div class="modal fade" id="salarySheetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Generate Salary Sheet</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="salarySheetForm" action="{{ route('payrolls.salary.sheet.export') }}" method="GET">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Month</label>
                        <input type="month" name="month" class="form-control" 
                               value="{{ date('Y-m') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Export Format</label>
                        <select name="format" class="form-select" required>
                            <option value="view">View in Browser</option>
                            <option value="pdf">Download PDF</option>
                            <option value="csv">Download CSV</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white">
                        <i class="fas fa-download me-1"></i> Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">


<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#payrollTable').DataTable({
        "paging": false,
        "searching": true,
        "ordering": true,
        "info": false,
        "responsive": true,
        "dom": '<"top"f>rt<"bottom"ilp><"clear">',
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Search payroll...",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "Showing 0 to 0 of 0 entries",
            "infoFiltered": "(filtered from _MAX_ total entries)"
        },
        "columnDefs": [
            { "orderable": false, "targets": [8] },
            { "className": "dt-nowrap", "targets": [1, 2] }
        ]
    });

    // Generate Payroll Form Submission
    $('#generatePayrollForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = $('#generatePayrollBtn');
        const originalBtnText = submitBtn.html();
        
        // Show loading state
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: form.action,
            method: 'POST',
            data: new FormData(form),
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.success) {
                    toastr.success(data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastr.error(data.message || 'Error generating payroll');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while generating payroll';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);
                console.error('Error:', xhr.responseText);
            },
            complete: function() {
                submitBtn.html(originalBtnText);
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Salary Sheet Form Submission
    $('#salarySheetForm').on('submit', function(e) {
        const format = $(this).find('[name="format"]').val();
        if (format === 'view') {
            e.preventDefault();
            const month = $(this).find('[name="month"]').val();
            window.location.href = `{{ route('payrolls.salary.sheet') }}?month=${month}`;
        }
    });

    // Modal open functions
    window.openGeneratePayroll = function() {
        new bootstrap.Modal(document.getElementById('generatePayrollModal')).show();
    };

    window.openSalarySheet = function() {
        new bootstrap.Modal(document.getElementById('salarySheetModal')).show();
    };
});


function viewPayroll(payrollId) {
    fetch(`/payroll/${payrollId}/view`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('payrollDetailsContent').innerHTML = data;
            new bootstrap.Offcanvas(document.getElementById('viewPayrollCanvas')).show();
        });
}

// Add this to your DataTable initialization to make rows clickable
$(document).ready(function() {
    $('#payrollTable').on('click', 'tr', function() {
        const payrollId = $(this).data('id');
        if (payrollId) {
            viewPayroll(payrollId);
        }
    });
});


// Make deduction cells clickable
$(document).on('click', '.deduction-cell', function() {
    const payrollId = $(this).data('payroll-id');
    if (payrollId) {
        window.location.href = `payroll/${payrollId}/deductions`;
    }
});




</script>

@endsection