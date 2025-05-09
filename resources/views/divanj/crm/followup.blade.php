@extends('layouts.app')

@section('title', 'Followups')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header py-3 px-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                <h3 class="mb-0 text-primary">Pending Followups</h3>
            </div>

            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Date Range Filter -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form id="dateRangeForm" class="row g-2">
                            <div class="col-auto">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ now()->toDateString() }}">
                            </div>
                            <div class="col-auto">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ now()->toDateString() }}">
                            </div>
                            <div class="col-auto align-self-end">
                                <button type="button" class="btn btn-primary" id="filterBtn">Filter</button>
                                <button type="button" class="btn btn-secondary" id="resetBtn">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="followupsTable" class="table table-hover table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Lead Name</th>
                                <th>Mobile No</th>
                                <th>Land Phone</th>
                                <th>Schedule Date</th>
                                <th>Schedule Time</th>
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

<!-- Add Call Report Modal -->
<div class="modal fade" id="addCallReportModal" tabindex="-1" aria-labelledby="addCallReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCallReportModalLabel">Add Call Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="callReportForm" action="{{ route('divanj.crm.call-report.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="lead_id" id="modal_lead_id">
                    <div class="mb-3">
                        <label for="medium" class="form-label">Medium</label>
                        <select class="form-control" id="medium" name="medium" required>
                            <option value=""></option>
                            <option value="call">Call</option>
                            <option value="text">Text</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="call_status_id" class="form-label">Call Status</label>
                        <select class="form-control" id="call_status_id" name="call_status_id" required>
                            <option value=""></option>
                            @foreach($callStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="schedule_date" class="form-label">Followup Date</label>
                        <input type="date" class="form-control" id="schedule_date" name="schedule_date">
                    </div>
                    <div class="mb-3">
                        <label for="schedule_time" class="form-label">Followup Time</label>
                        <input type="time" class="form-control" id="schedule_time" name="schedule_time">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Call Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#followupsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("divanj.crm.followups.index") }}',
                type: 'GET',
                data: function(d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                },
                error: function(xhr, error, thrown) {
                    console.log('AJAX Error: ', xhr.status, xhr.responseText);
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'lead.name', name: 'lead.name' },
                { data: 'lead.mobile', name: 'lead.mobile' },
                { data: 'lead.landline', name: 'lead.landline' },
                { data: 'schedule_date', name: 'schedule_date' },
                { data: 'schedule_time', name: 'schedule_time' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            pageLength: 10,
            order: [[2, 'asc']]
        });

        // Filter by date range
        $('#filterBtn').on('click', function() {
            table.ajax.reload(null, false); // false means don't reset paging
        });

        // Reset to today's date
        $('#resetBtn').on('click', function() {
            var today = new Date().toISOString().split('T')[0];
            $('#start_date').val(today);
            $('#end_date').val(today);
            table.ajax.reload(null, false);
        });

        // Handle Call Report Modal
        $(document).on('click', '.call-report-btn', function() {
            var leadId = $(this).data('lead-id');
            $('#modal_lead_id').val(leadId);
            $('#addCallReportModal').modal('show');
        });

        // On modal close, reload the table
        $('#addCallReportModal').on('hidden.bs.modal', function() {
            table.ajax.reload();
        });
    });
</script>
@endsection