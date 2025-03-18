@extends('layouts.app')

@section('title', 'Employees')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Employees</h3>
                    <a href="javascript:void(0);" class="btn btn-primary"
                       data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_employee">
                       <i class="ti ti-square-rounded-plus me-2"></i>Add Employee
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <table id="employeeTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Client</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td>{{ $employee->id }}</td>
                                    <td>{{ $employee->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $employee->user?->email ?? 'N/A' }}</td>
                                    <td>{{ $employee->user?->phone ?? 'N/A' }}</td>
                                    <td>{{ $employee->position }}</td>
                                    <td>{{ $employee->department }}</td>
                                    <td>{{ $employee->client ? $employee->client->company : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



<!-- Offcanvas: Add Employee -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_employee">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Add New Employee</h5> 
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf

            <!-- User Selection -->
            <div class="mb-3">
                <label for="user_id" class="form-label">Select User</label>
                <select class="form-select" id="user_id" name="user_id">
                    <option value="">Select a User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }}) - {{ $user->phone }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Client Selection -->
            <div class="mb-3">
                <label for="client_id" class="form-label">Select Client</label>
                <select class="form-select" id="client_id" name="client_id">
                    <option value="">Select a Client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->company }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Employee Details -->
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" class="form-control" id="department" name="department">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="position" class="form-label">Position</label>
                        <input type="text" class="form-control" id="position" name="position">
                    </div>
                </div>
            </div>

            <!-- Salary Information -->
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="salary_amount" class="form-label">Salary Amount</label>
                        <input type="number" class="form-control" id="salary_amount" name="salary_amount">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="salary_type" class="form-label">Salary Type</label>
                        <select class="form-select" id="salary_type" name="salary_type">
                            <option value="">Select Salary Type</option>
                            <option value="monthly">Monthly</option>
                            <option value="hourly">Hourly</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <!-- Date of Hiring -->
                    <div class="mb-3">
                        <label for="date_of_hire" class="form-label">Date of Hiring</label>
                        <input type="date" class="form-control" id="date_of_hire" name="date_of_hire">
                    </div>
                </div>

                    <!-- Login Time -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="login_time" class="form-label">Login Time</label>
                        <input type="time" class="form-control" id="login_time" name="login_time">
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Create Employee</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>


@endsection

@section('js')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#employeeTable').DataTable();
    });
</script>
@endsection