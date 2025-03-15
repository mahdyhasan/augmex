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
                            <p>Access the {{ config('app.name', 'Augmex') }} panel using your email and passcode.</p>
                        </div>
                        <div class="mb-3">
                            <label class="col-form-label" for="email">Email Address</label>
                            <div class="position-relative">
                                    <span class="input-icon-addon">
                                        <i class="ti ti-mail"></i>
                                    </span>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required>
                            </div>
                            @error('password')
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
                            <!-- <div class="text-end">
                                <a href="forgot-password.html" class="text-primary fw-medium link-hover">Forgot Password?</a>
                            </div> -->
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary w-100">Sign In</button>
                        </div>
                        <!-- <div class="mb-3">
                            <h6>New on our platform?<a href="register.html" class="text-purple link-hover"> Create an account</a></h6>
                        </div>
                        <div class="form-set-login or-text mb-3">
                            <h4>OR</h4>
                        </div>
                        <div class="d-flex align-items-center justify-content-center flex-wrap mb-3">
                            <div class="text-center me-2 flex-fill">
                                <a href="javascript:void(0);"
                                   class="br-10 p-2 px-4 btn bg-pending  d-flex align-items-center justify-content-center">
                                    <img class="img-fluid m-1" src="{{ asset('public/assets/img/icons/facebook-logo.svg') }}" alt="Facebook">
                                </a>
                            </div>
                            <div class="text-center me-2 flex-fill">
                                <a href="javascript:void(0);"
                                   class="br-10 p-2 px-4 btn bg-white d-flex align-items-center justify-content-center">
                                    <img class="img-fluid  m-1" src="{{ asset('public/assets/img/icons/google-logo.svg') }}" alt="Facebook">
                                </a>
                            </div>
                            <div class="text-center flex-fill">
                                <a href="javascript:void(0);"
                                   class="bg-dark br-10 p-2 px-4 btn btn-dark d-flex align-items-center justify-content-center">
                                    <img class="img-fluid  m-1" src="{{ asset('public/assets/img/icons/apple-logo.svg') }}" alt="Apple">
                                </a>
                            </div>
                        </div> -->
                        <div class="text-center">
                            <p class="fw-medium text-gray">Copyright &copy; {{ date('Y') }} - {{ config('app.name', 'Augmex') }}</p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
