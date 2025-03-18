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
                <div class="mb-3">
                    <label for="company_name" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" required>
                </div>
                <div class="mb-3">
                    <label for="country" class="form-label">Country</label>
                    <input type="text" class="form-control" id="country" name="country" required>
                </div>
                <div class="mb-3">
                    <label for="kdm" class="form-label">KDM</label>
                    <input type="text" class="form-control" id="kdm" name="kdm" required>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="rate" class="form-label">Rate</label>
                    <input type="text" class="form-control" id="rate" name="rate" required>
                </div>
                <div class="mb-3">
                    <label for="currency" class="form-label">Currency</label>
                    <select class="form-control" id="currency" name="currency" required>
                        <option value="">Select Currency</option>
                        <option value="AUD">AUD</option>
                        <option value="GBP">GBP</option>
                        <option value="EURO">EURO</option>
                        <option value="USD">USD</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="rate_type" class="form-label">Rate Type</label>
                    <select class="form-control" id="rate_type" name="rate_type" required>
                        <option value="">Select Rate Type</option>
                        <option value="hourly">Hourly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="invoice_type" class="form-label">Invoice Type</label>
                    <select class="form-control" id="invoice_type" name="invoice_type" required>
                        <option value="">Select Invoice Type</option>
                        <option value="biweekly">Biweekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>




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