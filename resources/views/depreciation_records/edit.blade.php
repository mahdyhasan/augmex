@extends('layouts.app')

@section('title', 'Edit Depreciation Record')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Edit Depreciation Record</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('depreciation_records.update', $depreciationRecord->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="year" class="form-label">Year</label>
                        <input type="number" name="year" class="form-control" value="{{ $depreciationRecord->year }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="depreciation_amount" class="form-label">Depreciation Amount</label>
                        <input type="number" name="depreciation_amount" class="form-control" value="{{ $depreciationRecord->depreciation_amount }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Record</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
