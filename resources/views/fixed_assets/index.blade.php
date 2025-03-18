@extends('layouts.app')

@section('title', 'Fixed Assets')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Fixed Assets</h3>
                <a href="javascript:void(0);" class="btn btn-primary"
                   data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add_fixed_asset">
                   <i class="ti ti-square-rounded-plus me-2"></i> Add Fixed Asset
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Purchase Date</th>
                            <th>Cost</th>
                            <th>Useful Life (Years)</th>
                            <th>Depreciation Rate (%)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fixedAssets as $fixedAsset)
                            <tr>
                                <td>{{ $fixedAsset->id }}</td>
                                <td>{{ $fixedAsset->name }}</td>
                                <td>{{ $fixedAsset->purchase_date }}</td>
                                <td>{{ $fixedAsset->cost }}</td>
                                <td>{{ $fixedAsset->useful_life }}</td>
                                <td>{{ $fixedAsset->depreciation_rate }}%</td>
                                <td>
                                    <a href="{{ route('fixed_assets.edit', $fixedAsset->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="{{ route('depreciation_records.index', $fixedAsset->id) }}" class="btn btn-info btn-sm">Depreciation</a>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<!-- Offcanvas: Add Fixed Asset & Initial Depreciation -->
<div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add_fixed_asset">
    <div class="offcanvas-header border-bottom">
        <h5 class="fw-semibold">Add New Fixed Asset</h5> 
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form action="{{ route('fixed_assets.store') }}" method="POST">
            @csrf

            <!-- Asset Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Asset Name</label>
                <input type="text" class="form-control" id="name" name="name" required placeholder="Enter asset name">
            </div>

            <!-- Purchase Date -->
            <div class="mb-3">
                <label for="purchase_date" class="form-label">Purchase Date</label>
                <input type="date" class="form-control" id="purchase_date" name="purchase_date" required>
            </div>

            <!-- Cost -->
            <div class="mb-3">
                <label for="cost" class="form-label">Cost</label>
                <input type="number" class="form-control" id="cost" name="cost" step="0.01" required placeholder="Enter asset cost">
            </div>

            <!-- Useful Life -->
            <div class="mb-3">
                <label for="useful_life" class="form-label">Useful Life (Years)</label>
                <input type="number" class="form-control" id="useful_life" name="useful_life" min="1" required placeholder="Enter useful life in years">
            </div>

            <!-- Depreciation Rate -->
            <div class="mb-3">
                <label for="depreciation_rate" class="form-label">Depreciation Rate (%)</label>
                <input type="number" class="form-control" id="depreciation_rate" name="depreciation_rate" step="0.1" min="0" max="100" required placeholder="Enter depreciation rate">
            </div>

            <!-- Initial Depreciation Record -->
            <h5 class="mt-4">Initial Depreciation Record</h5>
            <div class="mb-3">
                <label for="depreciation_year" class="form-label">Year</label>
                <input type="number" class="form-control" id="depreciation_year" name="depreciation_year" value="{{ date('Y') }}" required>
            </div>

            <div class="mb-3">
                <label for="depreciation_amount" class="form-label">Depreciation Amount</label>
                <input type="number" class="form-control" id="depreciation_amount" name="depreciation_amount" step="0.01" required placeholder="Enter initial depreciation amount">
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Add Fixed Asset</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
