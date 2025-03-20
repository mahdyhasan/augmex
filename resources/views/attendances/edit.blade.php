@extends('layouts.app')

@section('title', 'Edit Attendance')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Edit Attendance</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())

                <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="employee">Employee Name</label>
                        <input type="text" class="form-control" value="{{ $attendance->employee->user->name ?? 'N/A' }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="text" class="form-control" value="{{ $attendance->date }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="check_in">Check In</label>
                        <input type="time" name="check_in" class="form-control" value="{{ $attendance->check_in }}">
                    </div>

                    <div class="form-group">
                        <label for="check_out">Check Out</label>
                        <input type="time" name="check_out" class="form-control" value="{{ $attendance->check_out }}">
                    </div>

                    <div class="form-group">
                        <label for="isLate">Is Late?</label>
                        <select name="isLate" class="form-control">
                            <option value="0" {{ $attendance->isLate == 0 ? 'selected' : '' }}>On Time</option>
                            <option value="1" {{ $attendance->isLate == 1 ? 'selected' : '' }}>Late</option>
                        </select>
                    </div>
                                        
                    <div class="form-group">
                        <label for="status_id">Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ $attendance->status_id == 1 ? 'selected' : '' }}>Present</option>
                            <option value="2" {{ $attendance->status_id == 2 ? 'selected' : '' }}>Absent</option>
                            <option value="3" {{ $attendance->status_id == 3 ? 'selected' : '' }}>Weekend</option>
                            <option value="4" {{ $attendance->status_id == 4 ? 'selected' : '' }}>Holiday</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">Update Attendance</button>
                    <a href="{{ route('attendance.index') }}" class="btn btn-secondary">Back</a>
                </form>

                @else
                    
                    <p> Accessible for Admin and HR Only</p>

                @endif
            </div>
        </div>
    </div>
</div>
@endsection
