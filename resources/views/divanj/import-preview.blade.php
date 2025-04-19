@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Sales Import Analysis</h3>
        </div>
        <div class="card-body">
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title">{{ count($newRecords) }}</h5>
                            <p class="card-text">New Records</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title">{{ count($recordsToDelete) }}</h5>
                            <p class="card-text">Records to Remove</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">{{ count($unchangedRecords) }}</h5>
                            <p class="card-text">Unchanged Records</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <h5 class="card-title">{{ count($unmatchedConsultants) }}</h5>
                            <p class="card-text">Unmatched Consultants</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($unmatchedConsultants))
                <div class="alert alert-warning mb-4">
                    <h5>Unmatched Consultants:</h5>
                    <ul>
                        @foreach($unmatchedConsultants as $consultant)
                            <li>{{ $consultant }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tabs for different record types -->
            <ul class="nav nav-tabs" id="importTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="new-tab" data-bs-toggle="tab" href="#new">
                        New Records ({{ count($newRecords) }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="delete-tab" data-bs-toggle="tab" href="#delete">
                        To Remove ({{ count($recordsToDelete) }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="unchanged-tab" data-bs-toggle="tab" href="#unchanged">
                        Unchanged ({{ count($unchangedRecords) }})
                    </a>
                </li>
            </ul>

            <div class="tab-content p-3 border border-top-0 rounded-bottom">
                <!-- New Records Tab -->
                <div class="tab-pane fade show active" id="new" role="tabpanel">
                    @include('divanj.import-preview-table', [
                        'records' => $newRecords,
                        'type' => 'new'
                    ])
                </div>

                <!-- Records to Delete Tab -->
                <div class="tab-pane fade" id="delete" role="tabpanel">
                    <div class="alert alert-info">
                        <input type="checkbox" id="select-all-delete" class="form-check-input">
                        <label for="select-all-delete" class="form-check-label">Select All</label>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>ID</th>
                                <th>Consultant</th>
                                <th>Date</th>
                                <th>Product</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recordsToDelete as $record)
                            <tr class="table-danger">
                                <td>
                                    <input type="checkbox" 
                                           name="records_to_delete[]" 
                                           value="{{ $record['id'] }}" 
                                           class="form-check-input delete-checkbox" checked>
                                </td>
                                <td>{{ $record['id'] }}</td>
                                <td>{{ $record['employee']['stage_name'] ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($record['date'])->format('Y-m-d') }}</td>
                                <td>{{ $record['name'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Unchanged Records Tab -->
                <div class="tab-pane fade" id="unchanged" role="tabpanel">
                    @include('divanj.import-preview-table', [
                        'records' => $unchangedRecords,
                        'type' => 'unchanged'
                    ])
                </div>
            </div>

            <!-- Action Form -->
            <form action="{{ route('divanj.sales.process-import') }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="file_path" value="{{ $file }}">
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="import_new" id="import_new" value="1" checked>
                    <label class="form-check-label" for="import_new">
                        Import {{ count($newRecords) }} new records
                    </label>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="delete_missing" id="delete_missing">
                    <label class="form-check-label" for="delete_missing">
                        Remove {{ count($recordsToDelete) }} records not in file
                    </label>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('divanj.sales.report') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Confirm Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // "Select All" for deletion checkboxes
    $('#select-all-delete').change(function() {
        $('.delete-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    // Require at least one checkbox if deletion is enabled
    $('form').submit(function(e) {
        if ($('input[name="delete_missing"]:checked').length &&
            $('.delete-checkbox:checked').length === 0) {
            e.preventDefault();
            alert('Please select at least one record to delete');
        }
    });
});



document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
    });
</script>
@endsection
