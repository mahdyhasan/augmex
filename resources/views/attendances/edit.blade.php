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
                        <label for="status">Status</label>
                        <select name="status" class="form-control">
                            <option value="Present" {{ $attendance->status == 'Present' ? 'selected' : '' }}>Present</option>
                            <option value="Absent" {{ $attendance->status == 'Absent' ? 'selected' : '' }}>Absent</option>
                            <option value="Late" {{ $attendance->status == 'Late' ? 'selected' : '' }}>Late</option>
                            <option value="Late" {{ $attendance->status == 'Late' ? 'selected' : '' }}>Late</option>
                            <option value="Weekend" {{ $attendance->status == 'Weekend' ? 'selected' : '' }}>Weekend</option>
                            <option value="Holiday" {{ $attendance->status == 'Holiday' ? 'selected' : '' }}>Holiday</option>
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
