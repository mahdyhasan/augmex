@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="account-content">
        <div class="d-flex flex-wrap w-100 vh-100 overflow-hidden account-bg-01">
            <div class="d-flex align-items-center justify-content-center flex-wrap vh-100 overflow-auto p-4 w-50 bg-backdrop">
                <form action="{{ route('login') }}" method="POST" class="flex-fill">
                    @csrf
                    <div class="mx-auto mw-450">
                        <div class="text-center mb-4">
                            <img src="{{ asset('public/assets/img/logo.png') }}" class="img-fluid" alt="Logo">
                        </div>
                        <div class="mb-4">
                            <h4 class="mb-2 fs-20">Sign In</h4>
                            <p>Access the {{ config('app.name', 'Augmex') }} panel using your Phone No and Password.</p>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label" for="phone">Phone No</label>
                            <div class="position-relative">
                                <span class="input-icon-addon">
                                    <i class="ti ti-phone"></i>
                                </span>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" placeholder="Enter your phone number"
                                       value="{{ old('phone') }}" required autofocus>
                            </div>
                            @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="col-form-label" for="password">Password</label>
                            <div class="pass-group">
                                <input type="password" class="pass-input form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="********" required>
                                <span class="ti toggle-password ti-eye-off"></span>
                            </div>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="form-check form-check-md d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" value="" id="checkebox-md" checked="">
                                <label class="form-check-label" for="checkebox-md">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary w-100">Sign In</button>
                        </div>
                        <div class="text-center">
                            <p class="fw-medium text-gray">Copyright &copy; {{ date('Y') }} - {{ config('app.name', 'Augmex') }}</p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
