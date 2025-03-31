@extends('layouts.app')

@section('title', 'Clients')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Clients</h3>
                    <a href="javascript:void(0);" class="btn btn-primary"
                       data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_client">
                       <i class="ti ti-square-rounded-plus me-2"></i>Add Client
                    </a>
                </div>
                <div class="card-body">
                    <table id="dataTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Agency</th>
                                <th>Company</th>
                                <th>Country</th>
                                <th>KDM</th>
                                <th>Rate</th>
                                <th>Rate Type</th>
                                <th>Invoice Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                <tr>
                                    <td>{{ $client->id }}</td>
                                    <td>{{ $client->agency }}</td>
                                    <td>{{ $client->company }}</td>
                                    <td>{{ $client->country }}</td>
                                    <td>{{ $client->kdm }}</td>
                                    <td>{{ optional($client->clientConditions->first())->rate ?? 'N/A' }}</td>
                                    <td>{{ optional($client->clientConditions->first())->rate_type ?? 'N/A' }}</td>
                                    <td>{{ optional($client->clientConditions->first())->invoice_type ?? 'N/A' }}</td>
                                    <td>{{ $client->status == 1 ? 'Active' : 'Inactive' }}</td>
                                    <td>
                                        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-warning">Edit</a>

                                        <!-- Delete Button (soft delete) -->
                                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Offcanvas: Add Client -->
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_client">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-semibold">Add New Client</h5> 
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf
                <div class="modal-form-container">
                    <div class="row g-3">
                        <!-- First Column -->
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="agency" class="form-label">Agency</label>
                            <input type="text" class="form-control input-field" id="agency" name="agency" placeholder="Enter agency name">
                        </div>
                        
                        <div class="form-group">
                            <label for="company_name" class="form-label">Company Name <span class="required">*</span></label>
                            <input type="text" class="form-control input-field" id="company_name" name="company_name" required placeholder="Enter company name">
                        </div>
                        
                        <div class="form-group">
                            <label for="country" class="form-label">Country <span class="required">*</span></label>
                            <input type="text" class="form-control input-field" id="country" name="country" required placeholder="Enter country">
                        </div>
                        
                        <div class="form-group">
                            <label for="kdm" class="form-label">KDM <span class="required">*</span></label>
                            <input type="text" class="form-control input-field" id="kdm" name="kdm" required placeholder="Enter KDM">
                        </div>
                        </div>
                        
                        <!-- Second Column -->
                        <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="form-label">Status <span class="required">*</span></label>
                            <select class="form-select input-field" id="status" name="status" required>
                            <option value="">Select status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="rate" class="form-label">Rate <span class="required">*</span></label>
                            <div class="input-group">
                            <input type="text" class="form-control input-field" id="rate" name="rate" required placeholder="0.00">
                            <select class="form-select input-field currency-select" id="currency" name="currency" required>
                                <option value="">Currency</option>
                                <option value="AUD">AUD</option>
                                <option value="GBP">GBP</option>
                                <option value="EURO">EURO</option>
                                <option value="USD">USD</option>
                            </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="rate_type" class="form-label">Rate Type <span class="required">*</span></label>
                            <select class="form-select input-field" id="rate_type" name="rate_type" required>
                            <option value="">Select rate type</option>
                            <option value="hourly">Hourly</option>
                            <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="invoice_type" class="form-label">Invoice Type <span class="required">*</span></label>
                            <select class="form-select input-field" id="invoice_type" name="invoice_type" required>
                            <option value="">Select invoice type</option>
                            <option value="biweekly">Biweekly</option>
                            <option value="monthly">Monthly</option>
                            </select>
                        </div>
                        </div>
                    </div>
                    </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>




@endsection

@section ('css')

<style>
    .modal-form-container {
    padding: 20px;
    }

    .form-group {
    margin-bottom: 1.5rem;
    }

    .form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
    }

    .required {
    color: #dc3545;
    }

    .input-field {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    transition: border-color 0.2s;
    }

    .input-field:focus {
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    outline: none;
    }

    .form-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 16px 12px;
    }

    .input-group {
    display: flex;
    }

    .input-group .form-control {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    }

    .input-group .currency-select {
    width: 120px;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    border-left: none;
    }
</style>

@endsection


@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>


    <script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
    </script>



@endsection