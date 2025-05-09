@extends('layouts.app')

@section('title', 'Callback Sheet')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header py-3 px-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                <h3 class="mb-0 text-primary">Callback Sheet</h3>
            </div>

            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="callbackSheetTable" class="table table-hover table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Lead Name</th>
                                <th>Mobile</th>
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
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .btn-sm {
        font-size: 0.9rem;
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

        $('#callbackSheetTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("divanj.crm.call-back-sheet") }}',
                type: 'GET',
                error: function(xhr, error, thrown) {
                    console.log('AJAX Error: ', xhr.status, xhr.responseText);
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'lead.name', name: 'lead.name' },
                { data: 'lead.mobile', name: 'lead.mobile' },
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