@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="container-fluid">
    <div class="row no-gutter">
        <!-- The image half -->
        <div class="col-md-6 d-none d-md-flex bg-orange">
            <div class="d-flex align-items-center justify-content-center p-5 h-100">
                <div class="text-white text-center">
                    <h1 class="display-1 mb-4 text-white">Welcome to {{ config('app.name', 'Augmex') }}</h1>
                    <p class="lead mb-4">Access your personalized dashboard with all the tools you need.</p>
                    <div class="mt-5">
                        <i class="fas fa-mobile-alt fa-5x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- The content half -->
        <div class="col-md-6 bg-light">
            <div class="login d-flex align-items-center py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-10 col-xl-7 mx-auto">
                            <div class="text-center mb-4">
                                <!-- <img src="{{ asset('public/assets/img/logo.png') }}" class="img-fluid" alt="Logo" style="max-height: 60px;"> -->
                            </div>
                            
                            <h3 class="text-center mb-4">Sign In</h3>
                            <p class="text-muted text-center mb-4">Access the panel using your Phone No and Password.</p>
                            
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        </div>
                                        <input id="phone" type="text" 
                                               class="form-control @error('phone') is-invalid @enderror" 
                                               name="phone" value="{{ old('phone') }}" 
                                               placeholder="Enter your phone number" required autofocus>
                                        @error('phone')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input id="password" type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               name="password" placeholder="Enter your password" required>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="remember" name="remember" checked>
                                        <label class="custom-control-label" for="remember">Remember me</label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-dark btn-block mb-3">Sign In</button>
                                
                                <div class="text-center small text-muted mt-4">
                                    Copyright &copy; {{ date('Y') }} - {{ config('app.name', 'Lemon Infosys') }}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


@endsection


@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

</script>
@endsection