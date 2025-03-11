@extends('auth.layouts.app')

@section('title', 'Login')

@section('content')
    <!-- Start wrapper -->
    <div id="wrapper" class="bg-dark min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card card-authentication1 mx-auto my-5 animated zoomIn bg-dark border-0 shadow-lg">
            <div class="card-body p-4">
                <div class="card-content p-3">
                    <div class="text-center mb-4">
                        @if($settings && $settings->logo)
                            <img width="200" src="{{ asset($settings->logo) }}" alt="{{ $settings->company_name }} Logo" class="mb-3"/>
                        @else
                            <img width="200" src="{{ asset('public/assets/img/logo.png') }}" alt="Default Logo" class="mb-3"/>
                        @endif
                        <hr class="border-secondary">
                    </div>
                    <div class="card-title text-uppercase text-center py-2 text-white">{{ $settings->company_name ?? 'Company Name' }} Login</div>

                    @if(session('message'))
                        <div class="alert alert-{{ session('class') }} alert-dismissible fade show" role="alert">
                            <div class="alert-icon contrast-alert">
                                <i class="icon-close"></i>
                            </div>
                            <div class="alert-message">
                                <span>{{ session('message') }}</span>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="post" action="{{ route('login') }}" class="color-form">
                        @csrf
                        <div class="form-group mb-3">
                            <div class="position-relative has-icon-left">
                                <label for="email" class="sr-only">Email</label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required autofocus>
                                <div class="form-control-position">
                                    <i class="icon-user"></i>
                                </div>
                            </div>
                            @error('email')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="position-relative has-icon-left">
                                <label for="password" class="sr-only">Password</label>
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                                <div class="form-control-position">
                                    <i class="icon-lock"></i>
                                </div>
                            </div>
                            @error('password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-row mb-3">
                            <div class="form-group col-6">
                                <div class="demo-checkbox">
                                    <input type="checkbox" id="remember" name="remember" class="filled-in chk-col-danger" checked/>
                                    <label for="remember" class="text-white">Remember me</label>
                                </div>
                            </div>
                            <!-- Uncomment if you want to add a "Forgot Password" link -->
                            <!-- <div class="form-group col-6 text-right">
                                <a href="{{ route('password.request') }}" class="text-white">Forgot Password?</a>
                            </div> -->
                        </div>

                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-danger btn-block waves-effect waves-light">Sign In</button>
                        </div>

                        <div class="form-group text-center">
                            <hr class="border-secondary">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="darkmodal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-dark"></div>
            </div>
        </div>

        <!-- Back To Top Button -->
        <a href="javascript:void(0);" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>
    </div><!-- End wrapper -->
@endsection