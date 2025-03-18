@extends('layouts.app')

@section('title', 'Depreciation Records for ' . $fixedAsset->name)

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Depreciation Records for {{ $fixedAsset->name }}</h3>
                <a href="javascript:void(0);" class="btn btn-primary"
                   data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_depreciation">
                   <i class="ti ti-square-rounded-plus me-2"></i> Add Depreciation Record
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Depreciation Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($depreciationRecords as $record)
                            <tr>
                                <td>{{ $record->year }}</td>
                                <td>{{ $record->depreciation_amount }}</td>
                                <td>
                                    <a href="{{ route('depreciation_records.edit', $record->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('depreciation_records.destroy', $record->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
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

<!-- Offcanvas: Add Depreciation Record -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_depreciation">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Add New Depreciation Record</h5> 
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('depreciation_records.store', $fixedAsset->id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="year" class="form-label">Year</label>
                <input type="number" class="form-control" id="year" name="year" value="{{ date('Y') }}" required>
            </div>

            <div class="mb-3">
                <label for="depreciation_amount" class="form-label">Depreciation Amount</label>
                <input type="number" class="form-control" id="depreciation_amount" name="depreciation_amount" step="0.01" required placeholder="Enter depreciation amount">
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Add Record</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
