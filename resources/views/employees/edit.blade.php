@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')

@if(Auth::user()->isSuperAdmin() || Auth::user()->isHR())

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Employee</h5>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">Back to Employees</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('employees.update', $employee->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <!-- User Information -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" value="{{ $employee->user?->name ?? 'N/A' }}" inactive>                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" value="{{ $employee->user?->email ?? 'N/A' }}" inactive>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" value="{{ $employee->user?->phone ?? 'N/A' }}" inactive>
                                </div>

                                <!-- Employee Details -->
                                <div class="mb-3">
                                    <label for="department" class="form-label">Department</label>
                                    <input type="text" class="form-control" id="department" name="department" value="{{ $employee->department }}" >
                                </div>

                                <div class="mb-3">
                                    <label for="position" class="form-label">Position</label>
                                    <input type="text" class="form-control" id="position" name="position" value="{{ $employee->position }}" >
                                </div>

                                <!-- Salary Information -->
                                <div class="mb-3">
                                    <label for="salary_amount" class="form-label">Salary Amount</label>
                                    <input type="number" class="form-control" id="salary_amount" name="salary_amount" value="{{ $employee->salary_amount }}" >
                                </div>

                                <div class="mb-3">
                                    <label for="salary_type" class="form-label">Salary Type</label>
                                    <select class="form-select" id="salary_type" name="salary_type" >
                                        <option value="monthly" @if($employee->salary_type == 'monthly') selected @endif>Monthly</option>
                                        <option value="hourly" @if($employee->salary_type == 'hourly') selected @endif>Hourly</option>
                                    </select>
                                </div>

                                <!-- Client Selection -->
                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Client</label>
                                    <select class="form-select" id="client_id" name="client_id" >
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" @if($employee->client_id == $client->id) selected @endif>{{ $client->company }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="stage_name" class="form-label">Stage Name</label>
                                    <input type="text" class="form-control" id="stage_name" name="stage_name" value="{{ $employee->stage_name }}" >
                                </div>


                                <!-- Login Time -->
                                <div class="mb-3">
                                    <label for="login_time" class="form-label">Login Time</label>
                                    <input type="time" class="form-control" id="login_time" name="login_time" value="{{ $employee->login_time }}">
                                </div>

                                <!-- Date of Hire -->
                                <div class="mb-3">
                                    <label for="date_of_hire" class="form-label">Date of Hire</label>
                                    <input type="date" class="form-control" id="date_of_hire" name="date_of_hire" value="{{ $employee->date_of_hire }}" >
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <!-- Emergency Contact Information -->
                                <div class="mb-3">
                                    <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                                    <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{ $employee->emergency_contact_name }}">
                                </div>

                                <div class="mb-3">
                                    <label for="emergency_contact_relationship" class="form-label">Emergency Contact Relationship</label>
                                    <input type="text" class="form-control" id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ $employee->emergency_contact_relationship }}">
                                </div>

                                <div class="mb-3">
                                    <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                                    <input type="text" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ $employee->emergency_contact_phone }}">
                                </div>

                                <!-- Address Information -->
                                <div class="mb-3">
                                    <label for="address_line_1" class="form-label">Address Line 1</label>
                                    <input type="text" class="form-control" id="address_line_1" name="address_line_1" value="{{ $employee->address_line_1 }}">
                                </div>

                                <div class="mb-3">
                                    <label for="address_line_2" class="form-label">Address Line 2</label>
                                    <input type="text" class="form-control" id="address_line_2" name="address_line_2" value="{{ $employee->address_line_2 }}">
                                </div>

                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" value="{{ $employee->city }}">
                                </div>

                                <div class="mb-3">
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ $employee->postal_code }}">
                                </div>

                                <div class="mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <select class="form-control" id="country" name="country">
                                        <option value="Bangladesh" {{ $employee->country == 'Bangladesh' ? 'selected' : '' }}>Bangladesh</option>
                                        <option value="Other" {{ $employee->country == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>


                                <!-- Resume/CV Upload -->
                                <div class="mb-3">
                                    <label for="resume_cv" class="form-label">Resume/CV</label>
                                    <input type="file" class="form-control" id="resume_cv" name="resume_cv">
                                    @if($employee->resume_cv)
                                        <a href="{{ asset('storage/app/public/resumes/' . $employee->resume_cv) }}" target="_blank" class="mt-2 d-block">View Current Resume</a>
                                    @endif
                                </div>

                                <!-- Notes -->
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="1">{{ $employee->notes }}</textarea>
                                </div>

                                <!-- Date of Termination -->
                                <div class="mb-3">
                                    <label for="date_of_termination" class="form-label text-danger">Date of Termination</label>
                                    <input type="date" class="form-control" id="date_of_termination" name="date_of_termination" value="{{ $employee->date_of_termination }}">
                                </div>
                            </div><!-- Right column end -->
                        </div>

                                <!-- Buttons -->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Update Employee</button>
                                </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endif

@endsection