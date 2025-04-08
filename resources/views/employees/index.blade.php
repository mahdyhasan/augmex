@extends('layouts.app')

@section('title', 'Employees Management')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="mb-0">
                        <i class="fas fa-users me-2"></i>Employees
                    </h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                        <i class="fas fa-plus me-2"></i>Add Employee
                    </button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body ">
                <div class="table-responsive">
                    <table id="employeeTable" class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Contact</th>
                                <th>Position</th>
                                <th>Hiring Date</th>
                                <th>Client</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td>{{ $employee->id }}</td>
                                    <td>
                                        <!-- <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial bg-primary rounded-circle">
                                                    {{ substr($employee->user?->name ?? 'N/A', 0, 1) }}
                                                </span>
                                            </div> -->
                                            <div>
                                                <h6 class="mb-0">{{ $employee->user?->name ?? 'N/A' }}</h6>
                                                <small class="text-muted">{{ $employee->stage_name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div><i class="fas fa-envelope me-2 text-muted"></i>{{ $employee->user?->email ?? 'N/A' }}</div>
                                        <div><i class="fas fa-phone me-2 text-muted"></i>{{ $employee->user?->phone ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0">{{ $employee->position ?? 'N/A' }}</h6>
                                        <small class="text-muted">{{ $employee->department ?? 'N/A'}}</small>
                                    </td>
                                    <td>
                                        @if($employee->hire_date)
                                            @php
                                                $diffInMonths = $employee->hire_date->diffInMonths(now());
                                                $diffInWeeks = $employee->hire_date->diffInWeeks(now());
                                            @endphp
                                            
                                            <h6 class="mb-0">{{ $diffInMonths }} Months (~{{ $diffInWeeks }} weeks)</h6>
                                            <small class="text-muted">Employed since {{ $employee->hire_date->format('M d, Y') }}</small>
                                        @else
                                            <h6 class="mb-0">N/A</h6>
                                            <small class="text-muted">Hire date not available</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee->client)
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-building me-1"></i> {{ $employee->client->company }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee->date_of_termination)
                                            <span class="badge bg-danger">Terminated</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('employees.edit', $employee->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               data-bs-toggle="tooltip" 
                                               data-bs-title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-secondary view-employee" 
                                                    data-id="{{ $employee->id }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#employeeModal-{{ $employee->id }}"
                                                    data-bs-tooltip="tooltip" 
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">
                        <i class="fas fa-user-plus me-2"></i>Add New Employee
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('employees.store') }}" method="POST" id="employeeForm">
                    @csrf
                    <div class="modal-body">
                        <ul class="nav nav-tabs mb-4" id="employeeTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                                    Basic Info
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="employment-tab" data-bs-toggle="tab" data-bs-target="#employment" type="button" role="tab">
                                    Employment
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                                    Contact Info
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="employeeTabsContent">
                            <!-- Basic Info Tab -->
                            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="user_id" name="user_id" required>
                                                <option value="">Select User</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="user_id">User Account</label>
                                            @error('user_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="stage_name" name="stage_name" value="{{ old('stage_name') }}">
                                            <label for="stage_name">Stage Name (Optional)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                            <label for="date_of_birth">Date of Birth</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="gender" name="gender">
                                                <option value="">Select Gender</option>
                                                <option value="Male" @selected(old('gender') == 'male')>Male</option>
                                                <option value="Female" @selected(old('gender') == 'female')>Female</option>
                                                <option value="Other" @selected(old('gender') == 'other')>Other</option>
                                            </select>
                                            <label for="gender">Gender</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="married" name="married">
                                                <option value="">Select Marital Status</option>
                                                <option value="Yes" @selected(old('married') == 'Yes')>Yes</option>
                                                <option value="No" @selected(old('married') == 'No')>No</option>
                                            </select>
                                            <label for="married">Married?</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="nid_number" name="nid_number" value="{{ old('nid_number') }}">
                                            <label for="nid_number">NID Number</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Employment Tab -->
                            <div class="tab-pane fade" id="employment" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="client_id" name="client_id">
                                                <option value="">Select Client</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                                        {{ $client->company }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="client_id">Client</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                        <select class="form-control" id="department" name="department" required>
                                            <option value="">Select Department</option>
                                            <option value="Management">Management</option>
                                            <option value="HR">HR</option>
                                            <option value="IT">IT</option>
                                            <option value="Finance">Finance</option>
                                            <option value="Sales">Sales</option>
                                            <option value="Operations">Operations</option>
                                        </select>
                                            <label for="department">Department</label>
                                            @error('department')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                        <select class="form-control" id="position" name="position" required>
                                            <option value="">Select Position</option>
                                            <option value="Manager">Manager</option>
                                            <option value="Asst. Manager">Asst. Manager</option>
                                            <option value="Team Leader">Team Leader</option>
                                            <option value="Sr. Executive">Sr. Executive</option>
                                            <option value="Executive">Executive</option>
                                            <option value="Jr. Executive">Jr. Executive</option>
                                        </select>
                                            <label for="position">Position</label>
                                            @error('position')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="date" class="form-control" id="date_of_hire" name="date_of_hire" value="{{ old('date_of_hire') }}" required>
                                            <label for="date_of_hire">Date of Hire</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="time" class="form-control" id="login_time" name="login_time" value="{{ old('login_time') }}">
                                            <label for="login_time">Login Time</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="salary_type" name="salary_type">
                                                <option value="">Select Salary Type</option>
                                                <option value="monthly" @selected(old('salary_type') == 'monthly')>Monthly</option>
                                                <option value="hourly" @selected(old('salary_type') == 'hourly')>Hourly</option>
                                            </select>
                                            <label for="salary_type">Salary Type</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="number" class="form-control" id="salary_amount" name="salary_amount" value="{{ old('salary_amount') }}" step="0.01">
                                            <label for="salary_amount">Salary Amount</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Info Tab -->
                            <div class="tab-pane fade" id="contact" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="address_line_1" name="address_line_1" value="{{ old('address_line_1') }}">
                                            <label for="address_line_1">Address Line 1</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="address_line_2" name="address_line_2" value="{{ old('address_line_2') }}">
                                            <label for="address_line_2">Address Line 2</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}">
                                            <label for="city">City</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                                            <label for="postal_code">Postal Code</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="country" name="country" value="{{ old('country') }}">
                                            <label for="country">Country</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}">
                                            <label for="emergency_contact_name">Emergency Contact Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                        <select class="form-control" id="emergency_contact_relationship" name="emergency_contact_relationship">
                                            <option value="">Select Relationship</option>
                                            <option value="Parent">Parent</option>
                                            <option value="Spouse">Spouse</option>
                                            <option value="Sibling">Sibling</option>
                                            <option value="Friend">Friend</option>
                                            <option value="Other">Other</option>
                                        </select>
                                            <label for="emergency_contact_relationship">Relationship</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}">
                                            <label for="emergency_contact_phone">Emergency Contact Phone</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    @foreach($employees as $employee)
    <!-- Employee View Modal -->
    <div class="modal fade" id="employeeModal-{{ $employee->id }}" tabindex="-1" aria-labelledby="employeeModalLabel-{{ $employee->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-3">
                <div class="modal-header ">
                    <h2 class="modal-title" id="employeeModalLabel-{{ $employee->id }}">
                        <i class="fas fa-user-tie me-2"></i> {{ $employee->user->name ?? 'N/A' }}
                    </h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xxl bg-primary mb-2">
                            <span class="avatar-initial fs-1">
                                {{ substr($employee->user->name ?? 'N/A', 0, 1) }}
                            </span>
                        </div>
                        <h3>{{ $employee->user->name ?? 'N/A' }}</h3>
                        <div class="d-flex justify-content-center gap-2">
                            <span class="badge {{ $employee->date_of_termination ? 'bg-danger' : 'bg-success' }}">
                                {{ $employee->date_of_termination ? 'Terminated' : 'Active' }}
                            </span>
                            <span class="badge bg-dark">{{ $employee->position ?? 'N/A' }}</span>
                            <span class="badge bg-secondary">{{ $employee->department ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs mb-4" id="employeeTabs-{{ $employee->id }}" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab-{{ $employee->id }}" data-bs-toggle="tab" 
                                    data-bs-target="#profile-pane-{{ $employee->id }}" type="button" role="tab">
                                <i class="fas fa-user-circle me-1"></i> Profile
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="attendance-tab-{{ $employee->id }}" data-bs-toggle="tab" 
                                    data-bs-target="#attendance-pane-{{ $employee->id }}" type="button" role="tab">
                                <i class="fas fa-calendar-check me-1"></i> Attendance
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="payroll-tab-{{ $employee->id }}" data-bs-toggle="tab" 
                                    data-bs-target="#payroll-pane-{{ $employee->id }}" type="button" role="tab">
                                <i class="fas fa-money-bill-wave me-1"></i> Payroll
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="employeeTabContent-{{ $employee->id }}">
                        <!-- Profile Tab Content -->
                        <div class="tab-pane fade show active" id="profile-pane-{{ $employee->id }}" role="tabpanel">
                            <div class="row g-3">
                                <!-- Basic Information Card -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                                        </div>
                                        <div class="card-body">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-5 text-muted">Stage Name:</dt>
                                                <dd class="col-sm-7">{{ $employee->stage_name ?? 'N/A' }}</dd>

                                                <dt class="col-sm-5 text-muted">Date of Birth:</dt>
                                                <dd class="col-sm-7">{{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('M d, Y') : 'N/A' }}</dd>

                                                <dt class="col-sm-5 text-muted">Gender:</dt>
                                                <dd class="col-sm-7">{{ $employee->gender ? ucfirst($employee->gender) : 'N/A' }}</dd>

                                                <dt class="col-sm-5 text-muted">Marital Status:</dt>
                                                <dd class="col-sm-7">{{ $employee->married ? 'Married' : 'Single' }}</dd>

                                                <dt class="col-sm-5 text-muted">NID Number:</dt>
                                                <dd class="col-sm-7">{{ $employee->nid_number ?? 'N/A' }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>

                                <!-- Employment Details Card -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Employment Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-5 text-muted">Date of Hire:</dt>
                                                <dd class="col-sm-7">{{ $employee->date_of_hire ? \Carbon\Carbon::parse($employee->date_of_hire)->format('M d, Y') : 'N/A' }}</dd>

                                                <dt class="col-sm-5 text-muted">Login Time:</dt>
                                                <dd class="col-sm-7">{{ $employee->login_time ?? 'N/A' }}</dd>

                                                <dt class="col-sm-5 text-muted">Salary Type:</dt>
                                                <dd class="col-sm-7">{{ $employee->salary_type ? ucfirst($employee->salary_type) : 'N/A' }}</dd>

                                                <dt class="col-sm-5 text-muted">Salary Amount:</dt>
                                                <dd class="col-sm-7">{{ $employee->salary_amount ? 'Tk' . number_format($employee->salary_amount, 2) : 'N/A' }}</dd>

                                                <dt class="col-sm-5 text-muted">Client:</dt>
                                                <dd class="col-sm-7">{{ $employee->client->company ?? 'N/A' }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information Card -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0"><i class="fas fa-address-card me-2"></i>Contact Information</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="mb-3"><i class="fas fa-envelope me-2"></i>Primary Contact</h6>
                                                    <dl class="row mb-0">
                                                        <dt class="col-sm-4 text-muted">Email:</dt>
                                                        <dd class="col-sm-8">{{ $employee->user->email ?? 'N/A' }}</dd>

                                                        <dt class="col-sm-4 text-muted">Phone:</dt>
                                                        <dd class="col-sm-8">{{ $employee->user->phone ?? 'N/A' }}</dd>
                                                    </dl>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="mb-3"><i class="fas fa-home me-2"></i>Address</h6>
                                                    <address>
                                                        {{ $employee->address_line_1 ?? '' }}<br>
                                                        {{ $employee->address_line_2 ?? '' }}<br>
                                                        {{ $employee->city ?? '' }}, {{ $employee->postal_code ?? '' }}<br>
                                                        {{ $employee->country ?? '' }}
                                                    </address>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Emergency Contact Card -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Emergency Contact</h5>
                                        </div>
                                        <div class="card-body">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-2 text-muted">Name:</dt>
                                                <dd class="col-sm-4">{{ $employee->emergency_contact_name ?? 'N/A' }}</dd>

                                                <dt class="col-sm-2 text-muted">Relationship:</dt>
                                                <dd class="col-sm-4">{{ $employee->emergency_contact_relationship ?? 'N/A' }}</dd>

                                                <dt class="col-sm-2 text-muted">Phone:</dt>
                                                <dd class="col-sm-4">{{ $employee->emergency_contact_phone ?? 'N/A' }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Tab Content -->
                        <div class="tab-pane fade" id="attendance-pane-{{ $employee->id }}" role="tabpanel">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Attendance Records</h5>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-filter me-1"></i> Filter
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#" data-filter="all">All Records</a></li>
                                            <li><a class="dropdown-item" href="#" data-filter="month">This Month</a></li>
                                            <li><a class="dropdown-item" href="#" data-filter="late">Late Arrivals</a></li>
                                            <li><a class="dropdown-item" href="#" data-filter="absent">Absences</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id = "attendanceTable" class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Day</th>
                                                    <th>Check In</th>
                                                    <th>Check Out</th>
                                                    <th>Status</th>
                                                    <th>Hours</th>
                                                    <th>Late</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($employee->attendances as $attendance)
                                                <tr>
                                                    <td>{{ $attendance->date->format('d M y') }}</td>
                                                    <td>{{ $attendance->date->format('D') }}</td>
                                                    <td>{{ $attendance->check_in ?? '-' }}</td>
                                                    <td>{{ $attendance->check_out ?? '-' }}</td>
                                                    <td>
                                                        <span class="badge {{ $attendance->status_id ? 'bg-' . $attendance->status->color_class : 'bg-secondary' }}">
                                                            {{ $attendance->status->name ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($attendance->check_in && $attendance->check_out)
                                                            @php
                                                                $start = \Carbon\Carbon::parse($attendance->check_in);
                                                                $end = \Carbon\Carbon::parse($attendance->check_out);
                                                                $hours = $start->diffInHours($end);
                                                                $minutes = $start->diffInMinutes($end) % 60;
                                                            @endphp
                                                            {{ $hours }}h {{ $minutes }}m
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($attendance->isLate)
                                                            <span class="badge bg-warning text-dark">Yes</span>
                                                        @else
                                                            <span class="badge bg-success">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No attendance records found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payroll Tab Content -->
                        <div class="tab-pane fade" id="payroll-pane-{{ $employee->id }}" role="tabpanel">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Payroll History</h5>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-filter me-1"></i> Filter
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#">All Records</a></li>
                                            <li><a class="dropdown-item" href="#">This Year</a></li>
                                            <li><a class="dropdown-item" href="#">Paid</a></li>
                                            <li><a class="dropdown-item" href="#">Pending</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Period</th>
                                                    <th>Base Salary</th>
                                                    <th>Bonuses</th>
                                                    <th>Deductions</th>
                                                    <th>Net Salary</th>
                                                    <th>Status</th>
                                                    <th>Payment Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($employee->payrolls as $payroll)
                                                <tr>
                                                    <td>
                                                        {{ $payroll->pay_period_start->format('M d') }} - 
                                                        {{ $payroll->pay_period_end->format('M d, Y') }}
                                                    </td>
                                                    <td>Tk {{ number_format($payroll->base_salary, 2) }}</td>
                                                    <td>Tk {{ number_format($payroll->bonuses, 2) }}</td>
                                                    <td>Tk {{ number_format($payroll->deductions, 2) }}</td>
                                                    <td>Tk {{ number_format($payroll->net_salary, 2) }}</td>
                                                    <td>
                                                        <span class="badge {{ $payroll->payment_status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                                            {{ ucfirst($payroll->payment_status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ $payroll->payment_date ? $payroll->payment_date->format('M d, Y') : 'Pending' }}
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No payroll records found</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <button class="btn btn-primary" id="printEmployeeBtn-{{ $employee->id }}">
                        <i class="fas fa-print me-2"></i>Print Profile
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach


@endsection

@section('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
    .bg-light-blue {
        background-color: #e7f5ff;
        border-bottom: 1px solid #d0ebff;
    }
    .bg-light-green {
        background-color: #ebfbee;
        border-bottom: 1px solid #d3f9d8;
    }
    .bg-light-purple {
        background-color: #f3f0ff;
        border-bottom: 1px solid #e5dbff;
    }
    .bg-light-orange {
        background-color: #fff4e6;
        border-bottom: 1px solid #ffe8cc;
    }
    .avatar {
        width: 80px;
        height: 80px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .avatar-xxl {
        width: 100px;
        height: 100px;
    }
    .avatar-initial {
        font-weight: 600;
        color: white;
        text-transform: uppercase;
    }
    .offcanvas-xl {
        width: 800px;
        max-width: 90vw;
    }
    .card-header h5 {
        font-size: 1.1rem;
        font-weight: 600;
    }
    dt {
        font-weight: 500;
    }
</style>

<style>
        .avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .avatar-initial {
            font-weight: 600;
            color: white;
            text-transform: uppercase;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
        .nav-tabs .nav-link {
            color: #495057;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            font-weight: 600;
        }
    </style>
@endsection

@section('js')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#employeeTable').DataTable({
                responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 1 }, // Employee name
                    { responsivePriority: 2, targets: -1 }, // Actions
                    { responsivePriority: 3, targets: 2 }, // Contact
                    { orderable: false, targets: -1 } // Disable sorting for actions column
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search employees...",
                },
                dom: '<"top"f>rt<"bottom"lip><"clear">',
                initComplete: function() {
                    $('.dataTables_filter input').addClass('form-control');
                }
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize date pickers
            flatpickr("#date_of_hire", {
                dateFormat: "Y-m-d",
                allowInput: true
            });

            flatpickr("#date_of_birth", {
                dateFormat: "Y-m-d",
           
                allowInput: true,
                maxDate: "today"
            });

            // Form validation
            $('#employeeForm').on('submit', function(e) {
                let isValid = true;
                // Basic validation example
                if (!$('#user_id').val()) {
                    isValid = false;
                    $('#user_id').addClass('is-invalid');
                } else {
                    $('#user_id').removeClass('is-invalid');
                }
                
                if (!$('#department').val()) {
                    isValid = false;
                    $('#department').addClass('is-invalid');
                } else {
                    $('#department').removeClass('is-invalid');
                }
                
                if (!isValid) {
                    e.preventDefault();
                    // Switch to first tab with errors
                    $('#basic-tab').tab('show');
                }
            });
        });



        $(document).ready(function() {
        // Initialize the offcanvas
        const viewEmployeeOffcanvas = new bootstrap.Offcanvas(document.getElementById('viewEmployeeOffcanvas'));
        
        // Handle view employee button clicks
        $('.view-employee').on('click', function() {
            const employeeId = $(this).data('id');
            loadEmployeeData(employeeId);
            viewEmployeeOffcanvas.show();
        });
        
        // Function to load employee data
        function loadEmployeeData(employeeId) {
            // Show loading state
            $('#viewEmployeeOffcanvasLabel').html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
            
            // AJAX request to get employee data
            $.ajax({
                url: `/employees/${employeeId}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Update basic info
                    const employee = response.employee;
                    const user = employee.user || {};
                    const client = employee.client || {};
                    
                    // Set avatar initial
                    const initial = user.name ? user.name.charAt(0).toUpperCase() : '?';
                    $('#employeeAvatarInitial').text(initial);
                    
                    // Set basic info
                    $('#employeeName').text(user.name || 'N/A');
                    $('#employeeStageName').text(employee.stage_name || 'N/A');
                    $('#employeePosition').text(employee.position || 'N/A');
                    $('#employeeDepartment').text(employee.department || 'N/A');
                    $('#employeeStatus').text(employee.date_of_termination ? 'Terminated' : 'Active')
                        .removeClass('bg-success bg-danger')
                        .addClass(employee.date_of_termination ? 'bg-danger' : 'bg-success');
                    
                    // Set employment details
                    $('#employeeHireDate').text(employee.date_of_hire ? new Date(employee.date_of_hire).toLocaleDateString() : 'N/A');
                    $('#employeeLoginTime').text(employee.login_time || 'N/A');
                    $('#employeeSalaryType').text(employee.salary_type ? employee.salary_type.charAt(0).toUpperCase() + employee.salary_type.slice(1) : 'N/A');
                    $('#employeeSalaryAmount').text(employee.salary_amount ? '$' + parseFloat(employee.salary_amount).toFixed(2) : 'N/A');
                    $('#employeeClient').text(client.company || 'N/A');
                    
                    // Set personal info
                    $('#employeeDob').text(employee.date_of_birth ? new Date(employee.date_of_birth).toLocaleDateString() : 'N/A');
                    $('#employeeGender').text(employee.gender ? employee.gender.charAt(0).toUpperCase() + employee.gender.slice(1) : 'N/A');
                    $('#employeeMaritalStatus').text(employee.married ? 'Married' : 'Single');
                    $('#employeeNid').text(employee.nid_number || 'N/A');
                    
                    // Set contact info
                    $('#employeeEmail').text(user.email || 'N/A');
                    $('#employeePhone').text(user.phone || 'N/A');
                    
                    // Set address
                    let address = '';
                    if (employee.address_line_1) address += employee.address_line_1;
                    if (employee.address_line_2) address += '<br>' + employee.address_line_2;
                    if (employee.city) address += '<br>' + employee.city;
                    if (employee.postal_code) address += ', ' + employee.postal_code;
                    if (employee.country) address += '<br>' + employee.country;
                    $('#employeeAddress').html(address || 'N/A');
                    
                    // Set emergency contact
                    $('#employeeEmergencyName').text(employee.emergency_contact_name || 'N/A');
                    $('#employeeEmergencyRelation').text(employee.emergency_contact_relationship || 'N/A');
                    $('#employeeEmergencyPhone').text(employee.emergency_contact_phone || 'N/A');
                    
                    // Set edit button link
                    $('#editEmployeeBtn').attr('href', `/employees/${employeeId}/edit`);
                    
                    // Load attendance data
                    loadAttendanceData(employeeId);
                    
                    // Load payroll data
                    loadPayrollData(employeeId);
                    
                    // Update offcanvas title
                    $('#viewEmployeeOffcanvasLabel').html(`<i class="fas fa-user-tie me-2"></i>${user.name}'s Details`);
                },
                error: function(xhr) {
                    console.error('Error loading employee data:', xhr);
                    alert('Failed to load employee data. Please try again.');
                    viewEmployeeOffcanvas.hide();
                }
            });
        }
        
        // Function to load attendance data
        function loadAttendanceData(employeeId) {
            $.ajax({
                url: `/employees/${employeeId}/attendance`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    const attendanceRecords = response.attendance || [];
                    const $tbody = $('#attendanceRecords');
                    $tbody.empty();
                    
                    let presentCount = 0;
                    let absentCount = 0;
                    let lateCount = 0;
                    
                    attendanceRecords.forEach(record => {
                        const date = new Date(record.date);
                        const day = date.toLocaleDateString('en-US', { weekday: 'short' });
                        const checkIn = record.check_in ? formatTime(record.check_in) : '-';
                        const checkOut = record.check_out ? formatTime(record.check_out) : '-';
                        const status = record.status ? record.status.name : 'Absent';
                        const hours = calculateHours(record.check_in, record.check_out);
                        const isLate = record.isLate ? 'Yes' : 'No';
                        
                        if (status.toLowerCase() === 'present') presentCount++;
                        if (status.toLowerCase() === 'absent') absentCount++;
                        if (record.isLate) lateCount++;
                        
                        $tbody.append(`
                            <tr>
                                <td>${date.toLocaleDateString()}</td>
                                <td>${day}</td>
                                <td>${checkIn}</td>
                                <td>${checkOut}</td>
                                <td><span class="badge ${getStatusBadgeClass(status)}">${status}</span></td>
                                <td>${hours}</td>
                                <td>${isLate}</td>
                            </tr>
                        `);
                    });
                    
                    // Update stats
                    $('#presentCount').text(`${presentCount} Present`);
                    $('#absentCount').text(`${absentCount} Absent`);
                    $('#lateCount').text(`${lateCount} Late`);
                    
                    // Initialize DataTable if not already initialized
                    if (!$.fn.DataTable.isDataTable('#attendanceTable')) {
                        $('#attendanceTable').DataTable({
                            order: [[0, 'desc']],
                            pageLength: 10,
                            responsive: true
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error loading attendance data:', xhr);
                    $('#attendanceRecords').html('<tr><td colspan="7" class="text-center text-danger">Failed to load attendance data</td></tr>');
                }
            });
        }
        
        // Function to load payroll data
        function loadPayrollData(employeeId) {
            $.ajax({
                url: `/employees/${employeeId}/payroll`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    const payrollRecords = response.payroll || [];
                    const $tbody = $('#payrollRecords');
                    $tbody.empty();
                    
                    let totalPaid = 0;
                    let paymentCount = 0;
                    let highestPayment = 0;
                    let recentBonuses = [];
                    
                    payrollRecords.forEach(record => {
                        const startDate = new Date(record.pay_period_start);
                        const endDate = new Date(record.pay_period_end);
                        const period = `${startDate.toLocaleDateString()} - ${endDate.toLocaleDateString()}`;
                        const baseSalary = record.base_salary ? '$' + parseFloat(record.base_salary).toFixed(2) : '$0.00';
                        const bonuses = record.bonuses ? '$' + parseFloat(record.bonuses).toFixed(2) : '$0.00';
                        const deductions = record.deductions ? '$' + parseFloat(record.deductions).toFixed(2) : '$0.00';
                        const netSalary = record.net_salary ? '$' + parseFloat(record.net_salary).toFixed(2) : '$0.00';
                        const status = record.payment_status ? record.payment_status.charAt(0).toUpperCase() + record.payment_status.slice(1) : 'Pending';
                        const paymentDate = record.payment_date ? new Date(record.payment_date).toLocaleDateString() : 'Not Paid';
                        
                        // Calculate stats
                        if (record.payment_status === 'paid') {
                            totalPaid += parseFloat(record.net_salary) || 0;
                            paymentCount++;
                            
                            if (parseFloat(record.net_salary) > highestPayment) {
                                highestPayment = parseFloat(record.net_salary);
                            }
                            
                            if (parseFloat(record.bonuses) > 0) {
                                recentBonuses.push({
                                    period: period,
                                    amount: parseFloat(record.bonuses).toFixed(2)
                                });
                            }
                        }
                        
                        $tbody.append(`
                            <tr>
                                <td>${period}</td>
                                <td>${baseSalary}</td>
                                <td>${bonuses}</td>
                                <td>${deductions}</td>
                                <td>${netSalary}</td>
                                <td><span class="badge ${status === 'Paid' ? 'bg-success' : 'bg-warning'}">${status}</span></td>
                                <td>${paymentDate}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary view-payslip" data-id="${record.id}">
                                        <i class="fas fa-file-invoice"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                    
                    // Update stats
                    const avgSalary = paymentCount > 0 ? (totalPaid / paymentCount).toFixed(2) : 0;
                    $('#avgSalary').text('$' + avgSalary);
                    $('#totalPaid').text('$' + totalPaid.toFixed(2));
                    $('#highestPayment').text('$' + highestPayment.toFixed(2));
                    
                    // Update recent bonuses
                    const $recentBonuses = $('#recentBonuses');
                    if (recentBonuses.length > 0) {
                        $recentBonuses.empty();
                        recentBonuses.slice(0, 3).forEach(bonus => {
                            $recentBonuses.append(`
                                <div class="d-flex justify-content-between mb-1">
                                    <span>${bonus.period}</span>
                                    <span class="text-success">+$${bonus.amount}</span>
                                </div>
                            `);
                        });
                    } else {
                        $recentBonuses.text('No recent bonuses');
                    }
                    
                    // Initialize DataTable if not already initialized
                    if (!$.fn.DataTable.isDataTable('#payrollTable')) {
                        $('#payrollTable').DataTable({
                            order: [[0, 'desc']],
                            pageLength: 5,
                            responsive: true
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error loading payroll data:', xhr);
                    $('#payrollRecords').html('<tr><td colspan="8" class="text-center text-danger">Failed to load payroll data</td></tr>');
                }
            });
        }
        
        // Helper functions
        function formatTime(timeString) {
            if (!timeString) return '-';
            const [hours, minutes] = timeString.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 || 12;
            return `${displayHour}:${minutes} ${ampm}`;
        }
        
        function calculateHours(checkIn, checkOut) {
            if (!checkIn || !checkOut) return '-';
            
            const [inHour, inMinute] = checkIn.split(':').map(Number);
            const [outHour, outMinute] = checkOut.split(':').map(Number);
            
            let hours = outHour - inHour;
            let minutes = outMinute - inMinute;
            
            if (minutes < 0) {
                hours--;
                minutes += 60;
            }
            
            return `${hours}h ${minutes}m`;
        }
        
        function getStatusBadgeClass(status) {
            switch (status.toLowerCase()) {
                case 'present': return 'bg-success';
                case 'absent': return 'bg-danger';
                case 'late': return 'bg-warning text-dark';
                case 'half day': return 'bg-info';
                default: return 'bg-secondary';
            }
        }
        
        // Handle print button click
        $('#printEmployeeBtn').on('click', function() {
            // You can implement print functionality here
            window.print();
        });
    });

    $(document).on('click', '[id^="printEmployeeBtn-"]', function() {
        const employeeId = this.id.split('-').pop();
        const modalContent = $('#employeeModal-' + employeeId + ' .modal-content').clone();
        
        // Open print window
        const printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.write('<html><head><title>Employee Profile</title>');
        printWindow.document.write('<link href="{{ asset('public/assets/css/style.css') }}" rel="stylesheet">');
        printWindow.document.write('</head><body>');
        printWindow.document.write(modalContent.html());
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    });


// Attendance Tab Update

document.addEventListener('DOMContentLoaded', function() {
    // Load initial attendance data (last 7 days)
    loadAttendanceData('default');

    // Handle attendance filter clicks
    document.querySelectorAll('#attendance-pane-{{ $employee->id }} .dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.getAttribute('data-filter');
            loadAttendanceData(filter);
        });
    });
    
    function loadAttendanceData(filter) {
        fetch(`/employees/{{ $employee->id }}/attendance?filter=${filter}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                updateAttendanceTable(data.attendance);
            })
            .catch(error => {
                console.error('Error fetching attendance data:', error);
                const tbody = document.querySelector('#attendance-pane-{{ $employee->id }} tbody');
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading attendance data</td></tr>';
            });
    }
    
    function updateAttendanceTable(attendances) {
        const tbody = document.querySelector('#attendance-pane-{{ $employee->id }} tbody');
        tbody.innerHTML = '';
        
        if (attendances.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No attendance records found</td></tr>';
            return;
        }
        
        attendances.forEach(attendance => {
            // Format date
            const date = new Date(attendance.date);
            const formattedDate = date.toLocaleDateString('en-US', { 
                day: 'numeric', 
                month: 'short', 
                year: '2-digit' 
            });
            const dayName = date.toLocaleDateString('en-US', { weekday: 'short' });
            
            // Calculate hours if check_in and check_out exist
            let hours = '-';
            if (attendance.check_in && attendance.check_out) {
                const start = new Date(attendance.check_in);
                const end = new Date(attendance.check_out);
                const diffMs = end - start;
                const diffHrs = Math.floor(diffMs / 3600000);
                const diffMins = Math.round((diffMs % 3600000) / 60000);
                hours = `${diffHrs}h ${diffMins}m`;
            }
            
            // Determine status badge
            const statusName = attendance.status?.name || 'N/A';
            const statusClass = attendance.status ? `bg-${attendance.status.color_class}` : 'bg-secondary';
            
            const row = `
                <tr>
                    <td>${formattedDate}</td>
                    <td>${dayName}</td>
                    <td>${attendance.check_in || '-'}</td>
                    <td>${attendance.check_out || '-'}</td>
                    <td>
                        <span class="badge ${statusClass}">
                            ${statusName}
                        </span>
                    </td>
                    <td>${hours}</td>
                    <td>
                        <span class="badge ${attendance.isLate ? 'bg-warning text-dark' : 'bg-success'}">
                            ${attendance.isLate ? 'Yes' : 'No'}
                        </span>
                    </td>
                </tr>
            `;
            
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }
});



    </script>
@endsection