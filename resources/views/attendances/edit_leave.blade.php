@extends('layouts.app')

@section('title', 'Edit Leave')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Leave
                    </h3>
                    <a href="{{ route('attendance.leaves') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to Leaves
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('leaves.update', $leave->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())
                        <div class="col-md-6 mb-3">
                            <label for="employee_id" class="form-label">Employee</label>
                            <select name="employee_id" id="employee_id" class="form-control" required>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" 
                                        {{ $leave->employee_id == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @else
                            <input type="hidden" name="employee_id" value="{{ $leave->employee_id }}">
                        @endif

                        <div class="col-md-6 mb-3">
                            <label for="status_id" class="form-label">Leave Type</label>
                            <select name="status_id" id="status_id" class="form-control" required>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type->id }}" 
                                        {{ $leave->status_id == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" 
                                   class="form-control" 
                                   value="{{ old('start_date', $leave->start_date->format('Y-m-d')) }}" 
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" 
                                   class="form-control" 
                                   value="{{ old('end_date', $leave->end_date->format('Y-m-d')) }}" 
                                   required>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="reason" class="form-label">Reason</label>
                            <textarea name="reason" id="reason" class="form-control" 
                                      rows="3">{{ old('reason', $leave->reason) }}</textarea>
                        </div>

                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input type="hidden" name="approved" value="0"> <!-- This ensures a value is always sent -->
                                <input class="form-check-input" type="checkbox" 
                                    name="approved" id="approved" value="1"
                                    {{ old('approved', $leave->approved) ? 'checked' : '' }}>
                                <label class="form-check-label" for="approved">Approved</label>
                            </div>
                        </div>
                        @endif

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Update Leave
                            </button>
                            <a href="{{ route('attendance.leaves') }}" class="btn btn-secondary px-4">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection