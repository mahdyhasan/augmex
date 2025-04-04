@extends('layouts.app')

@section('title', 'Leave Management')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-gradient-primary text-white shadow-sm">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-3">
                        <div class="mb-2 mb-md-0">
                            <h3 class="fw-bold mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>Leave Management
                            </h3>
                        </div>
                        
                        <div class="d-flex flex-wrap gap-2">
                            @if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())
                                <button class="btn btn-white text-primary shadow-sm" 
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_leave">
                                    <i class="fas fa-plus-circle me-2"></i>Add Leave
                                </button>
                            @else
                                <button class="btn btn-white text-primary shadow-sm" 
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvas_apply_leave">
                                    <i class="fas fa-plus-circle me-2"></i>Apply for Leave
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- Filter Form for HR/Admin -->
                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())
                    <form id="filterForm" method="GET" action="{{ route('attendance.leaves') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="employee_name">Employee</label>
                                <select name="employee_id" id="employee_name" class="form-control">
                                    <option value="">-- All Employees --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status">Status</label>
                                <select name="approved" id="status" class="form-control">
                                    <option value="">-- All Status --</option>
                                    <option value="0" {{ request('approved') === '0' ? 'selected' : '' }}>Pending</option>
                                    <option value="1" {{ request('approved') === '1' ? 'selected' : '' }}>Approved</option>
                                </select>
                            </div>
                            <div class="col-md-12 mt-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="{{ route('attendance.leaves') }}" class="btn btn-dark">Reset</a>
                            </div>
                        </div>
                    </form>
                    @endif

                    <!-- Leaves Table -->
                    <table id="leavesTable" class="table table-striped">
                        <thead>
                            <tr>
                                @if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())
                                <th>Employee</th>
                                @endif
                                <th>Leave Type</th>
                                <th>Dates</th>
                                <th>Days</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaves as $leave)
                                <tr>
                                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())
                                    <td>{{ $leave->employee->user->name ?? 'N/A' }}</td>
                                    @endif
                                    <td>{{ $leave->status->name }}</td>
                                    <td>
                                        {{ $leave->start_date->format('j M Y') }} - 
                                        {{ $leave->end_date->format('j M Y') }}
                                    </td>
                                    <td>{{ $leave->start_date->diffInDays($leave->end_date) + 1 }}</td>
                                    <td>{{ Str::limit($leave->reason, 30) }}</td>
                                    <td>
                                        @if($leave->approved)
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">

                                            @if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())
                                                @if(!$leave->approved)
                                                    <form action="{{ route('leaves.approve', $leave->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <a href="{{ route('leaves.edit', $leave->id) }}" 
                                                    class="btn btn-sm btn-primary" 
                                                    title="Edit"
                                                    target="_blank">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="{{ route('leaves.destroy', $leave->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @else 
                                                @if(!$leave->approved)
                                                    <a href="{{ route('leaves.edit', $leave->id) }}" 
                                                        class="btn btn-sm btn-primary" 
                                                        title="Edit"
                                                        target="_blank">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            @endif
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

    <!-- Offcanvas: Apply Leave (for regular users) -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas_apply_leave">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-semibold">Apply for Leave</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('leaves.store') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" value="{{ Auth::user()->employee->id }}">
                <input type="hidden" name="approved" value="0">

                <div class="form-group mb-3">
                    <label for="apply_leave_type">Leave Type</label>
                    <select name="status_id" id="apply_leave_type" class="form-control" required>
                        <option value="">Select Leave Type</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="apply_start_date">Start Date</label>
                    <input type="date" name="start_date" id="apply_start_date" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label for="apply_end_date">End Date</label>
                    <input type="date" name="end_date" id="apply_end_date" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label for="apply_reason">Reason</label>
                    <textarea name="reason" id="apply_reason" class="form-control" rows="3" required></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Offcanvas: Add Leave (for HR/Admin) -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas_add_leave">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-semibold">Add Leave for Employee</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('leaves.store') }}" method="POST">
                @csrf

                <div class="form-group mb-3">
                    <label for="employee_id">Employee</label>
                    <select name="employee_id" id="employee_id" class="form-control" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="leave_type">Leave Type</label>
                    <select name="status_id" id="leave_type" class="form-control" required>
                        <option value="">Select Leave Type</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label for="reason">Reason</label>
                    <textarea name="reason" id="reason" class="form-control" rows="3"></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="approved">Approval Status</label>
                    <select name="approved" id="approved" class="form-control" required>
                        <option value="" >Is it approved?</option>
                        <option value="1" >Yes</option>
                        <option value="0" >No</option>
                    </select>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Leave</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </div>
            </form>
        </div>
    </div>


@endsection

@section('css')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('js')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#leavesTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "order": [[2, 'desc']] // Default sort by date
        });


        // Date validation for apply leave form
        $('#apply_start_date, #apply_end_date').change(function() {
            const startDate = new Date($('#apply_start_date').val());
            const endDate = new Date($('#apply_end_date').val());
            
            if (startDate && endDate && startDate > endDate) {
                alert('End date must be after start date');
                $('#apply_end_date').val('');
            }
        });

        // Date validation for add leave form (HR/Admin)
        $('#start_date, #end_date').change(function() {
            const startDate = new Date($('#start_date').val());
            const endDate = new Date($('#end_date').val());
            
            if (startDate && endDate && startDate > endDate) {
                alert('End date must be after start date');
                $('#end_date').val('');
            }
        });
    });



</script>
@endsection