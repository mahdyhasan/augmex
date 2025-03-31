@extends('layouts.app')

@section ('title', 'Edit Client')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h4 class="mb-0 font-weight-bold text-primary">Edit Client</h4>
                    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Clients
                    </a>
                </div>
                
                <div class="card-body px-4 py-4">
                    <form method="POST" action="{{ route('clients.update', $client->id) }}" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <!-- Agency -->
                                <div class="form-group mb-4">
                                    <label for="agency" class="form-label">Agency</label>
                                    <input type="text" 
                                           class="form-control @error('agency') is-invalid @enderror" 
                                           id="agency" 
                                           name="agency" 
                                           value="{{ old('agency', $client->agency) }}"
                                           placeholder="Enter agency name">
                                    @error('agency')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Company Name -->
                                <div class="form-group mb-4">
                                    <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" 
                                           name="company_name" 
                                           value="{{ old('company_name', $client->company) }}" 
                                           required
                                           placeholder="Enter company name">
                                    @error('company_name')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Country -->
                                <div class="form-group mb-4">
                                    <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('country') is-invalid @enderror" 
                                           id="country" 
                                           name="country" 
                                           value="{{ old('country', $client->country) }}" 
                                           required
                                           placeholder="Enter country">
                                    @error('country')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- KDM -->
                                <div class="form-group mb-4">
                                    <label for="kdm" class="form-label">KDM <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('kdm') is-invalid @enderror" 
                                           id="kdm" 
                                           name="kdm" 
                                           value="{{ old('kdm', $client->kdm) }}" 
                                           required
                                           placeholder="Enter KDM">
                                    @error('kdm')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <!-- Rate & Currency -->
                                <div class="form-group mb-4">
                                    <label for="rate" class="form-label">Rate <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" 
                                               step="0.01" 
                                               class="form-control @error('rate') is-invalid @enderror" 
                                               id="rate" 
                                               name="rate" 
                                               value="{{ old('rate', optional($client->clientConditions->first())->rate) }}" 
                                               required
                                               placeholder="0.00">
                                        <select class="form-control @error('currency') is-invalid @enderror" 
                                                id="currency" 
                                                name="currency" 
                                                required
                                                style="max-width: 120px;">
                                            @foreach(['AUD', 'GBP', 'EURO', 'USD'] as $currency)
                                                <option value="{{ $currency }}" 
                                                        {{ old('currency', optional($client->clientConditions->first())->currency) == $currency ? 'selected' : '' }}>
                                                    {{ $currency }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('rate')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                    @error('currency')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Rate Type -->
                                <div class="form-group mb-4">
                                    <label for="rate_type" class="form-label">Rate Type <span class="text-danger">*</span></label>
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
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Invoice Type -->
                                <div class="form-group mb-4">
                                    <label for="invoice_type" class="form-label">Invoice Type <span class="text-danger">*</span></label>
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
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="form-group mb-4">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="1" {{ $client->status == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $client->status == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-4 py-2">
                                <i class="fas fa-save mr-2"></i>Update Client
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .card {
        border-radius: 10px;
    }
    
    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .form-control {
        border-radius: 6px;
        padding: 0.75rem 1rem;
        border: 1px solid #ced4da;
        transition: all 0.2s;
    }
    
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.1);
    }
    
    .input-group .form-control:not(:last-child) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    
    .input-group .form-control:last-child {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-left: 0;
    }
    
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
        font-weight: 500;
    }
    
    .btn-outline-secondary {
        font-weight: 500;
    }
    
    .invalid-feedback {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.875rem;
    }
    
    .text-danger {
        color: #e74a3b !important;
    }
</style>
@endsection