@extends('layouts.app')

@section('title', 'Attendance Sheet')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Attendance Sheet</h3>
                </div>
                <div class="card-body">
                    <table id="attendanceTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Employee Name</th>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->id }}</td>
                                    <td>{{ $attendance->employee->name }}</td>
                                    <td>{{ $attendance->date }}</td>
                                    <td>{{ $attendance->check_in }}</td>
                                    <td>{{ $attendance->check_out }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($attendance->status == 'Present') badge-success
                                            @elseif($attendance->status == 'Absent') badge-danger
                                            @elseif($attendance->status == 'Late') badge-warning
                                            @else badge-info @endif">
                                            {{ $attendance->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@section('script')
<script>
    $(document).ready(function() {
        $('#attendanceTable').DataTable();
    });
</script>
@endsection
