@extends('layouts.app')

@section('title', 'Update User')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h4 class="mb-0 font-weight-bold text-primary">Edit User</h4>
                    <a href="{{ route('user.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Users
                    </a>
                </div>
                
                <div class="card-body px-4 py-4">
                    <form action="{{ route('user.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <!-- Name Field -->
                                <div class="form-group mb-4">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $user->name) }}" 
                                           required
                                           placeholder="Enter full name">
                                    @error('name')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Email Field -->
                                <div class="form-group mb-4">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" 
                                           required
                                           placeholder="Enter email address">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-group mb-4">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" 
                                            class="form-select @error('status') is-invalid @enderror" 
                                            required>
                                            <option value="">Select Status</option>
                                            <option value="1" {{ old('status', $user->status) == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status', $user->status) == '0' ? 'selected' : '' }}>InActive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <!-- Phone Field -->
                                <div class="form-group mb-4">
                                    <label for="phone" class="form-label">Phone (Username) <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" id="phone" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone', $user->phone) }}" 
                                           required
                                           placeholder="Enter phone number">
                                    @error('phone')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- User Type Field -->
                                <div class="form-group mb-4">
                                    <label for="user_type_id" class="form-label">User Role <span class="text-danger">*</span></label>
                                    <select name="user_type_id" id="user_type_id" 
                                            class="form-select @error('user_type_id') is-invalid @enderror" 
                                            required>
                                        @foreach($userTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('user_type_id', $user->user_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->role_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_type_id')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Password Update Section -->
                        <div class="card mt-3 mb-4 border-primary">
                            <div class="card-header bg-light">
                                <h5 class="mb-0 text-primary">Password Update (Leave blank to keep current password)</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" name="password" id="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   placeholder="Enter new password">
                                            @error('password')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                                   class="form-control" 
                                                   placeholder="Confirm new password">
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">Password must be at least 6 characters long</small>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mt-2">
                            <button type="submit" class="btn btn-primary btn-lg py-2">
                                <i class="fas fa-save mr-2"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection