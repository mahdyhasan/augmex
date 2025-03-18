@extends('layouts.app')

@section('title', 'Edit Fixed Asset')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Fixed Asset</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('fixed_assets.update', $fixedAsset->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">Asset Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $fixedAsset->name }}" required>
                        </div>

                        <div class="form-group">
                            <label for="purchase_date">Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control" value="{{ $fixedAsset->purchase_date }}" required>
                        </div>

                        <div class="form-group">
                            <label for="cost">Cost</label>
                            <input type="number" name="cost" class="form-control" value="{{ $fixedAsset->cost }}" required>
                        </div>

                        <div class="form-group">
                            <label for="depreciation_rate">Depreciation Rate (%)</label>
                            <input type="number" name="depreciation_rate" class="form-control" value="{{ $fixedAsset->depreciation_rate }}" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Asset</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
