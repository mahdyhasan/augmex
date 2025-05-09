@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header py-3 px-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                <h3 class="mb-0 text-primary">Payment Details</h3>
            </div>

            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('divanj.crm.payment.update', $lead->id) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="cardboard" class="form-label">Cardboard</label>
                            <input type="text" class="form-control" id="cardboard" name="cardboard" value="{{ old('cardboard', $payment->cardboard ?? '') }}" required>
                            <div class="invalid-feedback">Please provide a cardboard.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="card_last_four" class="form-label">Last Four Digits</label>
                            <input type="text" class="form-control" id="card_last_four" name="card_last_four" value="{{ old('card_last_four', $payment->card_last_four ?? '') }}" maxlength="4" required>
                            <div class="invalid-feedback">Please provide the last four digits.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="expiry_month" class="form-label">Expiry Month</label>
                            <input type="text" class="form-control" id="expiry_month" name="expiry_month" value="{{ old('expiry_month', $payment->expiry_month ?? '') }}" maxlength="2" required>
                            <div class="invalid-feedback">Please provide expiry month (MM).</div>
                        </div>
                        <div class="col-md-6">
                            <label for="expiry_year" class="form-label">Expiry Year</label>
                            <input type="text" class="form-control" id="expiry_year" name="expiry_year" value="{{ old('expiry_year', $payment->expiry_year ?? '') }}" maxlength="4" required>
                            <div class="invalid-feedback">Please provide expiry year (YYYY).</div>
                        </div>
                        <div class="col-md-6">
                            <label for="sivivi" class="form-label">SIVIVI</label>
                            <input type="text" class="form-control" id="sivivi" name="sivivi" value="{{ old('sivivi', $payment->sivivi ?? '') }}">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Payment</button>
                        <a href="{{ route('divanj.crm.leads.show', $lead->id) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .form-control:invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        display: none;
    }
    .form-control:invalid ~ .invalid-feedback {
        display: block;
    }
</style>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    (function() {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
@endsection