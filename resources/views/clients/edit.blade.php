@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Client</h5>
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary btn-sm">Back to Clients</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('clients.update', $client->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="company_name">Company Name</label>
                            <input type="text" 
                                   class="form-control @error('company_name') is-invalid @enderror" 
                                   id="company_name" 
                                   name="company_name" 
                                   value="{{ old('company_name', $client->company) }}" 
                                   required>
                            @error('company_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" 
                                   class="form-control @error('country') is-invalid @enderror" 
                                   id="country" 
                                   name="country" 
                                   value="{{ old('country', $client->country) }}" 
                                   required>
                            @error('country')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="kdm">KDM</label>
                            <input type="text" 
                                   class="form-control @error('kdm') is-invalid @enderror" 
                                   id="kdm" 
                                   name="kdm" 
                                   value="{{ old('kdm', $client->kdm) }}" 
                                   required>
                            @error('kdm')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="rate">Rate</label>
                            <input type="number" 
                                   step="0.01" 
                                   class="form-control @error('rate') is-invalid @enderror" 
                                   id="rate" 
                                   name="rate" 
                                   value="{{ old('rate', optional($client->clientConditions->first())->rate) }}" 
                                   required>
                            @error('rate')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select class="form-control @error('currency') is-invalid @enderror" 
                                    id="currency" 
                                    name="currency" 
                                    required>
                                <option value="">Select Currency</option>
                                @foreach(['AUD', 'GBP', 'EURO', 'USD'] as $currency)
                                    <option value="{{ $currency }}" 
                                            {{ old('currency', optional($client->clientConditions->first())->currency) == $currency ? 'selected' : '' }}>
                                        {{ $currency }}
                                    </option>
                                @endforeach
                            </select>
                            @error('currency')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="rate_type">Rate Type</label>
                            <select class="form-control @error('rate_type') is-invalid @enderror" 
                                    id="rate_type" 
                                    name="rate_type" 
                                    required>
                                <option value="">Select Rate Type</option>
                                @foreach(['hourly', 'monthly'] as $rateType)
                                    <option value="{{ $rateType }}" 
                                            {{ old('rate_type', optional($client->clientConditions->first())->rate_type) == $rateType ? 'selected' : '' }}>
                                        {{ ucfirst($rateType) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('rate_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="invoice_type">Invoice Type</label>
                            <select class="form-control @error('invoice_type') is-invalid @enderror" 
                                    id="invoice_type" 
                                    name="invoice_type" 
                                    required>
                                <option value="">Select Invoice Type</option>
                                @foreach(['biweekly', 'monthly'] as $invoiceType)
                                    <option value="{{ $invoiceType }}" 
                                            {{ old('invoice_type', optional($client->clientConditions->first())->invoice_type) == $invoiceType ? 'selected' : '' }}>
                                        {{ ucfirst($invoiceType) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('invoice_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="status" class="font-weight-bold">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="1" {{ $client->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $client->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                Update Client
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection