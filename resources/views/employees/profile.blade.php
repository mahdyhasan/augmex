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

                

                <form action="{{ route('employee.details.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Column 1 -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ $employeeDetail->date_of_birth ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="Male" {{ ($employeeDetail->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ ($employeeDetail->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ ($employeeDetail->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Marital Status</label>
                                <input type="text" name="marital_status" class="form-control" value="{{ $employeeDetail->marital_status ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label>Nationality</label>
                                <input type="text" name="nationality" class="form-control" value="{{ $employeeDetail->nationality ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label>NID Number</label>
                                <input type="text" name="nid_number" class="form-control" value="{{ $employeeDetail->nid_number ?? '' }}">
                            </div>
                        </div>

                        <!-- Column 2 -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Address Line 1</label>
                                <input type="text" name="address_line_1" class="form-control" value="{{ $employeeDetail->address_line_1 ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label>Address Line 2</label>
                                <input type="text" name="address_line_2" class="form-control" value="{{ $employeeDetail->address_line_2 ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label>City</label>
                                <input type="text" name="city" class="form-control" value="{{ $employeeDetail->city ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label>Postal Code</label>
                                <input type="text" name="postal_code" class="form-control" value="{{ $employeeDetail->postal_code ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label>Country</label>
                                <input type="text" name="country" class="form-control" value="{{ $employeeDetail->country ?? '' }}">
                            </div>
                        </div>

                        <!-- Column 3 -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="form-control" value="{{ $employeeDetail->emergency_contact_name ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label>Emergency Contact Relationship</label>
                                <input type="text" name="emergency_contact_relationship" class="form-control" value="{{ $employeeDetail->emergency_contact_relationship ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label>Emergency Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" class="form-control" value="{{ $employeeDetail->emergency_contact_phone ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label>Resume/CV (PDF/DOC)</label>
                                <input type="file" name="resume_cv" class="form-control">
                                @if($employeeDetail->resume_cv)
                                    <a href="{{ asset('assets/resumes/' . $employeeDetail->resume_cv) }}" target="_blank">View Uploaded Resume</a>
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Notes</label>
                                <textarea name="notes" class="form-control">{{ $employeeDetail->notes ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

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
