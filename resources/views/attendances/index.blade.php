@extends('layouts.app')

@section('title', 'Attendance Sheet')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-gradient-primary text-white shadow-sm">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-3">
                        <div class="mb-2 mb-md-0">
                            <h3 class="fw-bold mb-0">
                                <i class="fas fa-clipboard-list me-2"></i>Attendance Sheet
                            </h3>
                        </div>
                        
                        <div class="d-flex flex-wrap gap-2">
                            @if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())
                                <button class="btn btn-light text-primary shadow-sm" id="lateSummaryBtn" 
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvas_late_summary">
                                    <i class="fas fa-clock me-2"></i>Late Summary
                                </button>

                                <button class="btn btn-white text-primary shadow-sm" 
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_attendance">
                                    <i class="fas fa-plus-circle me-2"></i>Add Attendance
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

                    <!-- Filter Form -->
                    <form id="filterForm" method="GET" action="{{ route('attendance.index') }}" class="mb-4">
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
                                <select name="employee_name" id="employee_name" class="form-control">
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->user->name }}" {{ request('employee_name') == $employee->user->name ? 'selected' : '' }}>
                                            {{ $employee->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="{{ route('attendance.index') }}" class="btn btn-dark">Reset</a>
                            </div>
                        </div>
                    </form>


                    <!-- Delete Attendance in Bulk -->
                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())
                        @if(request()->has('start_date') || request()->has('end_date') || request()->has('employee_name'))
                            <div class="mb-3 text-end">
                                <button class="btn btn-danger" id="deleteFilteredBtn">
                                    <i class="fas fa-trash-alt me-2"></i>Delete Filtered Entries
                                </button>
                                <small class="text-muted d-block mt-1">This will delete all entries matching your current filters</small>
                            </div>
                        @endif
                    @endif


                    <!-- Attendance Table -->
                    <table id="attendanceTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Employee Name</th>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                                <th>Late</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->id }}</td>
                                    <td>{{ $attendance->employee->user->name ?? 'N/A' }}</td>
                                    <td>{{ $attendance->date->format('Y-m-d (D)') }}</td>
                                    <td>{{ $attendance->check_in }}</td>
                                    <td>{{ $attendance->check_out }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($attendance->status->name == 'Present') badge-success
                                            @elseif($attendance->status->name == 'Absent') badge-danger
                                            @elseif($attendance->status->name == 'Weekend') badge-info
                                            @elseif($attendance->status->name == 'Holiday') badge-warning
                                            @endif">
                                            {{ $attendance->status->name }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($attendance->isLate)
                                            <span class="badge badge-danger">Late</span>
                                        @else
                                            <span class="badge badge-success">On Time</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())
                                            <a href="{{ route('attendance.edit', $attendance->id) }}" class="btn btn-warning">Edit</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Offcanvas: Add Attendance -->
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_attendance">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-semibold">Add New Attendance</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="employee_id">Employee</label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->user->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mt-2">
                    <label for="date">Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>

                <div class="form-group mt-2">
                    <label for="check_in">Check In</label>
                    <input type="time" name="check_in" class="form-control">
                </div>

                <div class="form-group mt-2">
                    <label for="check_out">Check Out</label>
                    <input type="time" name="check_out" class="form-control">
                </div>

                <div class="form-group mt-2">
                    <label for="isLate">Is Late?</label>
                    <select name="isLate" class="form-control" required>
                        <option value="">Select On Time Status</option>
                        <option value="0">On Time</option>
                        <option value="1">Late</option>
                    </select>
                </div>

                <div class="form-group mt-2">
                    <label for="status">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="">Select Status</option>
                        @foreach($attendanceStatuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Save Attendance</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </div>
            </form>
        </div>
    </div>


    
    <!-- Offcanvas: Late Summary -->
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_late_summary">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-semibold">Late Summary </h5> - Everyone is given 5 minutes grace time
        </div>
        <div class="offcanvas-body">
            <div id="lateSummaryTableContainer">
                <p>Loading...</p>
            </div>
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
        // Initialize DataTable
        $('#attendanceTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "responsive": true,
            "order": [[2, 'desc']] // Default sort by date
        });

        // Submit filter form on change
        $('#date, #employee_name').on('change', function() {
            $('#filterForm').submit();
        });
    });

    // LATE SUMMARY
    document.getElementById('lateSummaryBtn').addEventListener('click', function() {
        const startDate = "{{ $startDate }}";
        const endDate = "{{ $endDate }}";
        const employeeName = "{{ $employeeName }}";

        const container = document.getElementById('lateSummaryTableContainer');
        container.innerHTML = "<p>Loading...</p>";

        fetch("{{ route('attendance.late.summary') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                start_date: startDate,
                end_date: endDate,
                employee_name: employeeName
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'error') {
                container.innerHTML = `<div class="alert alert-warning">${data.message}</div>`;
                return;
            }

            const rows = data.data.map(item => `
                <tr>
                    <td>${item.date}</td>
                    <td>${item.name}</td>
                    <td>${item.check_in ?? '-'}</td>
                    <td>${item.check_out ?? '-'}</td>
                    <td>${item.late_by} min</td>
                </tr>
            `).join('');

            const summary = Object.entries(data.late_summary).map(([name, count]) => `
                <p><strong>${name}</strong> was late on <strong>${count}</strong> day(s)</p>
            `).join('');

            container.innerHTML = `
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Employee</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Late By</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
                <div class="mt-4 border-top pt-3">
                    <h6 class="fw-bold text-primary">Late Summary by Employee</h6>
                    ${summary || '<p class="text-muted">No late records found.</p>'}
                </div>
            `;
        })
        .catch(err => {
            container.innerHTML = `<div class="alert alert-danger">Error loading data.</div>`;
            console.error(err);
        });
    });

    // Delete filtered entries functionality
    document.getElementById('deleteFilteredBtn')?.addEventListener('click', function() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const employeeName = document.getElementById('employee_name').value;
        
        // Count how many records match the filter
        const matchingCount = document.querySelectorAll('#attendanceTable tbody tr').length;
        
        if (matchingCount === 0) {
            alert('No records match your current filters.');
            return;
        }
        
        if (confirm(`Are you sure you want to delete ${matchingCount} attendance record(s)?\n\nThis action cannot be undone.`)) {
            // Show loading state
            const btn = this;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Deleting...';
            btn.disabled = true;
            
            // Send AJAX request
            fetch("{{ route('attendance.delete.filtered') }}", {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    start_date: startDate,
                    end_date: endDate,
                    employee_name: employeeName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload(); // Refresh to show changes
                } else {
                    alert('Error: ' + data.message);
                    btn.innerHTML = '<i class="fas fa-trash-alt me-2"></i>Delete Filtered Entries';
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting records.');
                btn.innerHTML = '<i class="fas fa-trash-alt me-2"></i>Delete Filtered Entries';
                btn.disabled = false;
            });
        }
    });
</script>
@endsection