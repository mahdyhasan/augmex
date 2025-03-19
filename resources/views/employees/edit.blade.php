@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Employee</h5>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">
                        Back to Employees
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('employees.update', $employee->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <!-- User Information -->
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input 
                                        type="text" 
                                        name="name" 
                                        class="form-control @error('name') is-invalid @enderror" 
                                        id="name"
                                        value="{{ old('name', $employee->user?->name) }}"
                                        placeholder="Enter user name"
                                    >
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email (Locked)</label>
                                    <input 
                                        type="email" 
                                        class="form-control" 
                                        id="email" 
                                        value="{{ $employee->user?->email ?? '' }}" 
                                        readonly
                                    >
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input 
                                        type="text" 
                                        name="phone" 
                                        class="form-control @error('phone') is-invalid @enderror" 
                                        id="phone"
                                        value="{{ old('phone', $employee->user?->phone) }}"
                                        placeholder="Enter phone number"
                                    >
                                    @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Department -->
                                <div class="mb-3">
                                    <label for="department" class="form-label">Department</label>
                                    <select 
                                        name="department" 
                                        class="form-select @error('department') is-invalid @enderror" 
                                        id="department"
                                    >
                                        <option value="">Select Department</option>
                                        <option value="HR" {{ old('department', $employee->department) == 'HR' ? 'selected' : '' }}>HR</option>
                                        <option value="IT" {{ old('department', $employee->department) == 'IT' ? 'selected' : '' }}>IT</option>
                                        <option value="Finance" {{ old('department', $employee->department) == 'Finance' ? 'selected' : '' }}>Finance</option>
                                        <option value="Marketing" {{ old('department', $employee->department) == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                        <option value="Operations" {{ old('department', $employee->department) == 'Operations' ? 'selected' : '' }}>Operations</option>
                                    </select>
                                    @error('department')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Position -->
                                <div class="mb-3">
                                    <label for="position" class="form-label">Position</label>
                                    <select 
                                        name="position" 
                                        class="form-select @error('position') is-invalid @enderror" 
                                        id="position"
                                    >
                                        <option value="">Select Position</option>
                                        <option value="Manager" {{ old('position', $employee->position) == 'Manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="Developer" {{ old('position', $employee->position) == 'Developer' ? 'selected' : '' }}>Developer</option>
                                        <option value="Designer" {{ old('position', $employee->position) == 'Designer' ? 'selected' : '' }}>Designer</option>
                                        <option value="Analyst" {{ old('position', $employee->position) == 'Analyst' ? 'selected' : '' }}>Analyst</option>
                                        <option value="Coordinator" {{ old('position', $employee->position) == 'Coordinator' ? 'selected' : '' }}>Coordinator</option>
                                    </select>
                                    @error('position')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Gender Field -->
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select 
                                        name="gender" 
                                        class="form-select @error('gender') is-invalid @enderror"
                                        id="gender"
                                    >
                                        <option value="">Select Gender</option>
                                        <option value="Male" {{ old('gender', $employee->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender', $employee->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender', $employee->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Marital Status Field -->
                                <div class="mb-3">
                                    <label for="married" class="form-label">Marital Status</label>
                                    <select 
                                        name="married" 
                                        class="form-select @error('married') is-invalid @enderror"
                                        id="married"
                                    >
                                        <option value="">Select Marital Status</option>
                                        <option value="Yes" {{ old('married', $employee->married) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ old('married', $employee->married) == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                    @error('married')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Salary Information -->
                                <div class="mb-3">
                                    <label for="salary_amount" class="form-label">Salary Amount</label>
                                    <input 
                                        type="number" 
                                        name="salary_amount" 
                                        class="form-control @error('salary_amount') is-invalid @enderror" 
                                        id="salary_amount"
                                        value="{{ old('salary_amount', $employee->salary_amount) }}"
                                    >
                                    @error('salary_amount')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="salary_type" class="form-label">Salary Type</label>
                                    <select 
                                        class="form-select @error('salary_type') is-invalid @enderror" 
                                        id="salary_type" 
                                        name="salary_type"
                                    >
                                        <option value="">Select Salary Type</option>
                                        <option value="monthly" {{ old('salary_type', $employee->salary_type) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="hourly" {{ old('salary_type', $employee->salary_type) == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                    </select>
                                    @error('salary_type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Client Selection -->
                                <div class="mb-3">
                                    <label for="client_id" class="form-label">Client</label>
                                    <select 
                                        class="form-select @error('client_id') is-invalid @enderror" 
                                        id="client_id" 
                                        name="client_id"
                                    >
                                        <option value="">Select Client</option>
                                        @forelse($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id', $employee->client_id) == $client->id ? 'selected' : '' }}>
                                                {{ $client->company }}
                                            </option>
                                        @empty
                                            <option value="">No Clients Available</option>
                                        @endforelse
                                    </select>
                                    @error('client_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="stage_name" class="form-label">Stage Name</label>
                                    <input 
                                        type="text" 
                                        name="stage_name" 
                                        class="form-control @error('stage_name') is-invalid @enderror" 
                                        id="stage_name"
                                        value="{{ old('stage_name', $employee->stage_name) }}"
                                    >
                                    @error('stage_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Login Time -->
                                <div class="mb-3">
                                    <label for="login_time" class="form-label">Login Time</label>
                                    <input 
                                        type="time" 
                                        name="login_time" 
                                        class="form-control @error('login_time') is-invalid @enderror" 
                                        id="login_time"
                                        value="{{ old('login_time', $employee->login_time) }}"
                                    >
                                    @error('login_time')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Date of Hire -->
                                <div class="mb-3">
                                    <label for="date_of_hire" class="form-label">Date of Hire</label>
                                    <input 
                                        type="date" 
                                        name="date_of_hire" 
                                        class="form-control @error('date_of_hire') is-invalid @enderror" 
                                        id="date_of_hire"
                                        value="{{ old('date_of_hire', $employee->date_of_hire) }}"
                                    >
                                    @error('date_of_hire')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- End Left Column -->

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <!-- Emergency Contact Information -->
                                <div class="mb-3">
                                    <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                                    <input 
                                        type="text" 
                                        name="emergency_contact_name" 
                                        class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                        id="emergency_contact_name"
                                        value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}"
                                    >
                                    @error('emergency_contact_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="emergency_contact_relationship" class="form-label">Emergency Contact Relationship</label>
                                    <select 
                                        name="emergency_contact_relationship" 
                                        class="form-select @error('emergency_contact_relationship') is-invalid @enderror"
                                        id="emergency_contact_relationship"
                                    >
                                        <option value="">Select Relationship</option>
                                        <option value="Parent" {{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) == 'Parent' ? 'selected' : '' }}>Parent</option>
                                        <option value="Spouse" {{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) == 'Spouse' ? 'selected' : '' }}>Spouse</option>
                                        <option value="Sibling" {{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                        <option value="Friend" {{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) == 'Friend' ? 'selected' : '' }}>Friend</option>
                                        <option value="Other" {{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('emergency_contact_relationship')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                                    <input 
                                        type="number" 
                                        name="emergency_contact_phone" 
                                        class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                        id="emergency_contact_phone"
                                        value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}"
                                    >
                                    @error('emergency_contact_phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Address Information -->
                                <div class="mb-3">
                                    <label for="address_line_1" class="form-label">Address Line 1</label>
                                    <input 
                                        type="text" 
                                        name="address_line_1" 
                                        class="form-control @error('address_line_1') is-invalid @enderror" 
                                        id="address_line_1"
                                        value="{{ old('address_line_1', $employee->address_line_1) }}"
                                    >
                                    @error('address_line_1')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="address_line_2" class="form-label">Address Line 2</label>
                                    <input 
                                        type="text" 
                                        name="address_line_2" 
                                        class="form-control @error('address_line_2') is-invalid @enderror" 
                                        id="address_line_2"
                                        value="{{ old('address_line_2', $employee->address_line_2) }}"
                                    >
                                    @error('address_line_2')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <select 
                                        name="city" 
                                        class="form-select @error('city') is-invalid @enderror"
                                        id="city"
                                    >
                                        <option value="">Select City</option>
                                        <option value="Dhaka" {{ old('city', $employee->city) == 'Dhaka' ? 'selected' : '' }}>Dhaka</option>
                                        <option value="Chittagong" {{ old('city', $employee->city) == 'Chittagong' ? 'selected' : '' }}>Chittagong</option>
                                        <option value="Sylhet" {{ old('city', $employee->city) == 'Sylhet' ? 'selected' : '' }}>Sylhet</option>
                                        <option value="Rajshahi" {{ old('city', $employee->city) == 'Rajshahi' ? 'selected' : '' }}>Rajshahi</option>
                                        <option value="Khulna" {{ old('city', $employee->city) == 'Khulna' ? 'selected' : '' }}>Khulna</option>
                                        <option value="Other" {{ old('city', $employee->city) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('city')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <input 
                                        type="number" 
                                        name="postal_code" 
                                        class="form-control @error('postal_code') is-invalid @enderror" 
                                        id="postal_code"
                                        value="{{ old('postal_code', $employee->postal_code) }}"
                                    >
                                    @error('postal_code')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <select 
                                        class="form-select @error('country') is-invalid @enderror" 
                                        id="country" 
                                        name="country"
                                    >
                                        <option value="">Select Country</option>
                                        <option value="Bangladesh" {{ old('country', $employee->country) == 'Bangladesh' ? 'selected' : '' }}>Bangladesh</option>
                                        <option value="Other" {{ old('country', $employee->country) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('country')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Resume/CV Upload -->
                                <div class="mb-3">
                                    <label for="resume_cv" class="form-label">Resume/CV (PDF or DOCX)</label>
                                    <input 
                                        type="file" 
                                        name="resume_cv" 
                                        class="form-control @error('resume_cv') is-invalid @enderror" 
                                        id="resume_cv"
                                    >
                                    @error('resume_cv')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    @if($employee->resume_cv)
                                        <a href="{{ asset('storage/resumes/' . $employee->resume_cv) }}" 
                                           target="_blank" 
                                           class="mt-2 d-block">
                                            View Current Resume
                                        </a>
                                    @endif
                                </div>

                                <!-- Notes -->
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea 
                                        name="notes" 
                                        class="form-control @error('notes') is-invalid @enderror" 
                                        id="notes" 
                                        rows="2"
                                    >{{ old('notes', $employee->notes) }}</textarea>
                                    @error('notes')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- Date of Birth -->
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input 
                                        type="date" 
                                        name="date_of_birth" 
                                        class="form-control @error('date_of_birth') is-invalid @enderror" 
                                        id="date_of_birth"
                                        value="{{ old('date_of_birth', $employee->date_of_birth) }}"
                                    >
                                    @error('date_of_birth')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- NID Number -->
                                <div class="mb-3">
                                    <label for="nid_number" class="form-label">NID Number</label>
                                    <input 
                                        type="number" 
                                        name="nid_number" 
                                        class="form-control @error('nid_number') is-invalid @enderror" 
                                        id="nid_number"
                                        value="{{ old('nid_number', $employee->nid_number) }}"
                                        placeholder="Enter NID Number"
                                    >
                                    @error('nid_number')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Date of Termination -->
                                <div class="mb-3">
                                    <label for="date_of_termination" class="form-label text-danger">Date of Termination</label>
                                    <input 
                                        type="date" 
                                        name="date_of_termination" 
                                        class="form-control @error('date_of_termination') is-invalid @enderror" 
                                        id="date_of_termination"
                                        value="{{ old('date_of_termination', $employee->date_of_termination) }}"
                                    >
                                    @error('date_of_termination')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- End Right Column -->
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Employee</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@endsection