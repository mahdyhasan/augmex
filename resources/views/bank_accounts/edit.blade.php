@extends('layouts.app')

@section('title', 'Edit Bank Account')

@section('content')
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Bank Account</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('bank_accounts.update', $bankAccount->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="bank_name">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ $bankAccount->bank_name }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="branch">Branch</label>
                            <input type="text" name="branch" class="form-control" value="{{ $bankAccount->branch }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="account_number">Account Number</label>
                            <input type="text" name="account_number" class="form-control" value="{{ $bankAccount->account_number }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="name">Account Holder's Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $bankAccount->name }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" class="form-control" rows="3" required>{{ $bankAccount->address }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="balance">Balance</label>
                            <input type="number" step="0.01" name="balance" class="form-control" value="{{ $bankAccount->balance }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select name="currency" class="form-control" required>
                                <option value="">Select currency</option>
                                <option value="USD" {{ $bankAccount->currency == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ $bankAccount->currency == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ $bankAccount->currency == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                <option value="BDT" {{ $bankAccount->currency == 'BDT' ? 'selected' : '' }}>BDT - Bangladeshi Taka</option>
                                <option value="AUD" {{ $bankAccount->currency == 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection