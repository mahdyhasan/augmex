@extends('layouts.app')

@section('title', 'Leads Management')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header py-3 px-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                <h3 class="mb-0 text-primary">My Leads</h3>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="leadsTable" class="table table-hover table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Landline</th>
                                <th>Source</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .btn-sm {
        font-size: 0.9rem;
    }
    .offcanvas {
        max-width: 500px;
    }
    .invalid-feedback {
        display: none;
        color: #dc3545;
        font-size: 0.875em;
    }
    .is-invalid ~ .invalid-feedback,
    .is-invalid ~ .text-muted + .invalid-feedback {
        display: block;
    }
    .text-muted {
        font-size: 0.8rem;
        color: #6c757d;
    }

</style>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#leadsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("divanj.crm.leads.index") }}',
                type: 'GET',
                error: function(xhr, error, thrown) {
                    console.log('AJAX Error: ', xhr.status, xhr.responseText);
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'mobile', name: 'mobile' },
                { data: 'landline', name: 'landline' },
                { data: 'source', name: 'source' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']]
        });
    });


</script>
@endsection