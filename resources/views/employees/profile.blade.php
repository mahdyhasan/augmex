@extends('layouts.app')

@section('title', 'Employee Profile')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Employee Profile</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('employees.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Column 1 -->
                            <div class="col-md-4">
                                <!-- Date of Hire -->
                                <div class="form-group">
                                    <label>Date of Hire</label>
                                    <input type="date" name="date_of_hire" class="form-control" value="{{ $employee->date_of_hire ?? '' }}" disabled>
                                </div>

                                <!-- Date of Birth -->
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control" value="{{ $employee->date_of_birth ?? '' }}">
                                </div>

                                <!-- Gender -->
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control">
                                        <option value="Male" {{ ($employee->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ ($employee->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ ($employee->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>

                                <!-- Marital Status -->
                                <div class="form-group">
                                    <label>Marital Status</label>
                                    <select name="married" class="form-control">
                                        <option value="Yes" {{ ($employee->married ?? '') == 'Yes' ? 'selected' : '' }}>Married</option>
                                        <option value="No" {{ ($employee->married ?? '') == 'No' ? 'selected' : '' }}>Unmarried</option>
                                    </select>
                                </div>

                                <!-- NID Number -->
                                <div class="form-group">
                                    <label>NID Number</label>
                                    <input type="text" name="nid_number" class="form-control" value="{{ $employee->nid_number ?? '' }}">
                                </div>
                            </div>

                            <!-- Column 2 -->
                            <div class="col-md-4">
                                <!-- Address Line 1 -->
                                <div class="form-group">
                                    <label>Address Line 1</label>
                                    <input type="text" name="address_line_1" class="form-control" value="{{ $employee->address_line_1 ?? '' }}">
                                </div>

                                <!-- Address Line 2 -->
                                <div class="form-group">
                                    <label>Address Line 2</label>
                                    <input type="text" name="address_line_2" class="form-control" value="{{ $employee->address_line_2 ?? '' }}">
                                </div>

                                <!-- City -->
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="city" class="form-control" value="{{ $employee->city ?? '' }}">
                                </div>

                                <!-- Postal Code -->
                                <div class="form-group">
                                    <label>Postal Code</label>
                                    <input type="text" name="postal_code" class="form-control" value="{{ $employee->postal_code ?? '' }}">
                                </div>

                                <!-- Country -->
                                <div class="form-group">
                                    <label>Country</label>
                                    <select class="form-control" id="country" name="country">
                                        <option value="Bangladesh" {{ $employee->country == 'Bangladesh' ? 'selected' : '' }}>Bangladesh</option>
                                        <option value="Other" {{ $employee->country == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Column 3 -->
                            <div class="col-md-4">
                                <!-- Emergency Contact Name -->
                                <div class="form-group">
                                    <label>Emergency Contact Name</label>
                                    <input type="text" name="emergency_contact_name" class="form-control" value="{{ $employee->emergency_contact_name ?? '' }}">
                                </div>

                                <!-- Emergency Contact Relationship -->
                                <div class="form-group">
                                    <label>Emergency Contact Relationship</label>
                                    <input type="text" name="emergency_contact_relationship" class="form-control" value="{{ $employee->emergency_contact_relationship ?? '' }}">
                                </div>

                                <!-- Emergency Contact Phone -->
                                <div class="form-group">
                                    <label>Emergency Contact Phone</label>
                                    <input type="text" name="emergency_contact_phone" class="form-control" value="{{ $employee->emergency_contact_phone ?? '' }}">
                                </div>

                                <!-- Resume/CV Upload -->
                                <div class="form-group">
                                    <label>Resume/CV (PDF/DOC)</label>
                                    <input type="file" name="resume_cv" class="form-control">
                                    @if($employee->resume_cv)
                                        <a href="{{ asset('storage/resumes/' . $employee->resume_cv) }}" target="_blank" class="mt-2 d-block">View Uploaded Resume</a>
                                    @endif
                                </div>

                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary mt-3">Update Details</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
@endsection