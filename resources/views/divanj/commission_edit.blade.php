@extends('layouts.app')

@section('title', 'Edit Commission')

@section('content')


<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Edit Commission for {{ $commission->employee->stage_name ?? $commission->employee->user->name }}</h4>
            <a href="javascript:void(0);" class="btn btn-primary"
                   data-bs-toggle="offcanvas" data-bs-target="#generateCommissionModal">
                   <i class="ti ti-square-rounded-plus me-2"></i> Generate Commission
                </a>
            </div>
            <div class="card-body">
    
                <form action="{{ route('divanj.commission.update', $commission->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Commission Amount</label>
                        <input type="number" step="0.01" name="commission_amount" value="{{ old('commission_amount', $commission->commission_amount) }}" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Commission Type</label>
                        <select name="commission_type" class="form-control" required>
                            <option value="fixed" {{ $commission->commission_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                            <option value="mixed" {{ $commission->commission_type == 'mixed' ? 'selected' : '' }}>Mixed</option>
                            <option value="weekend" {{ $commission->commission_type == 'weekend' ? 'selected' : '' }}>Weekend</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Update</button>
                    <a href="{{ route('divanj.commission.index') }}" class="btn btn-secondary mt-3">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>






@endsection

