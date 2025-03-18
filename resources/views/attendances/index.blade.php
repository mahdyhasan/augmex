@extends('layouts.app')

@section('title', 'Attendance Sheet')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3>Attendance Sheet</h3>
                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())
                            <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_attendance">
                            Add Attendance
                            </button>
                        @endif
                </div>
            </div>                

            
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <table id="attendanceTable" class="table table-striped table-bordered" style="width:100%">
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
                                <td>{{ $attendance->date }}</td>
                                <td>{{ $attendance->check_in }}</td>
                                <td>{{ $attendance->check_out }}</td>
                                <td>
                                    <span class="badge 
                                        @if($attendance->status == 'Present') badge-success
                                        @elseif($attendance->status == 'Absent') badge-danger
                                        @else badge-info @endif">
                                        {{ $attendance->status }}
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



    <!-- Offcanvas: Add Attendance-->
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
                <label for="status">Status</label>
                <select name="status" class="form-control" required>
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                    <option value="Late">Late</option>
                    <option value="Weekend">Weekend</option>
                    <option value="Holiday">Holiday</option>
                </select>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">Save Attendance</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>



@endsection


@section('script')

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>





<script>
    
    $(document).ready(function() {
        $('#attendanceTable').DataTable();

    });
</script>
@endsection
