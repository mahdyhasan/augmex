@extends('layouts.app')

@section('title', 'Employee Profile')

@section('content')

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">Employee Profile</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Success & Error Messages -->
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('employees.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Personal Details</h5>

                                <!-- Date of Hire -->
                                <div class="mb-3">
                                    <label class="form-label">Date of Hire</label>
                                    <input type="date" name="date_of_hire" class="form-control" value="{{ $employee->date_of_hire ?? '' }}" disabled>
                                </div>

                                <!-- Date of Birth -->
                                <div class="mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control" value="{{ $employee->date_of_birth ?? '' }}">
                                </div>

                                <!-- Gender -->
                                <div class="mb-3">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-control">
                                        <option value="Male" {{ ($employee->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ ($employee->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ ($employee->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>

                                <!-- Marital Status -->
                                <div class="mb-3">
                                    <label class="form-label">Marital Status</label>
                                    <select name="married" class="form-control">
                                        <option value="Yes" {{ ($employee->married ?? '') == 'Yes' ? 'selected' : '' }}>Married</option>
                                        <option value="No" {{ ($employee->married ?? '') == 'No' ? 'selected' : '' }}>Unmarried</option>
                                    </select>
                                </div>

                                <!-- NID Number -->
                                <div class="mb-3">
                                    <label class="form-label">NID Number</label>
                                    <input type="text" name="nid_number" class="form-control" value="{{ $employee->nid_number ?? '' }}">
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Contact Details</h5>

                                <!-- Address Line 1 -->
                                <div class="mb-3">
                                    <label class="form-label">Address Line 1</label>
                                    <input type="text" name="address_line_1" class="form-control" value="{{ $employee->address_line_1 ?? '' }}">
                                </div>

                                <!-- Address Line 2 -->
                                <div class="mb-3">
                                    <label class="form-label">Address Line 2</label>
                                    <input type="text" name="address_line_2" class="form-control" value="{{ $employee->address_line_2 ?? '' }}">
                                </div>

                                <!-- City -->
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" value="{{ $employee->city ?? '' }}">
                                </div>

                                <!-- Postal Code -->
                                <div class="mb-3">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="postal_code" class="form-control" value="{{ $employee->postal_code ?? '' }}">
                                </div>

                                <!-- Country -->
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <select name="country" class="form-control">
                                        <option value="Bangladesh" {{ ($employee->country ?? '') == 'Bangladesh' ? 'selected' : '' }}>Bangladesh</option>
                                        <option value="Other" {{ ($employee->country ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact Section -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Emergency Contact</h5>

                                <!-- Emergency Contact Name -->
                                <div class="mb-3">
                                    <label class="form-label">Emergency Contact Name</label>
                                    <input type="text" name="emergency_contact_name" class="form-control" value="{{ $employee->emergency_contact_name ?? '' }}">
                                </div>

                                <!-- Emergency Contact Relationship -->
                                <div class="mb-3">
                                    <label class="form-label">Emergency Contact Relationship</label>
                                    <input type="text" name="emergency_contact_relationship" class="form-control" value="{{ $employee->emergency_contact_relationship ?? '' }}">
                                </div>

                                <!-- Emergency Contact Phone -->
                                <div class="mb-3">
                                    <label class="form-label">Emergency Contact Phone</label>
                                    <input type="text" name="emergency_contact_phone" class="form-control" value="{{ $employee->emergency_contact_phone ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Additional Information</h5>

                                <!-- Resume Upload -->
                                <div class="mb-3">
                                    <label class="form-label">Resume/CV (PDF/DOC)</label>
                                    <input type="file" name="resume_cv" class="form-control">
                                    @if($employee->resume_cv)
                                        <a href="{{ asset('storage/resumes/' . $employee->resume_cv) }}" target="_blank" class="mt-2 d-block">View Uploaded Resume</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary">Update Details</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
