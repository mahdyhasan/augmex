@extends('layouts.app')

@section('title', 'Update User')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="mb-0">Edit User</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name Field -->
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>

                        <!-- Email Field -->
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>

                        <!-- Phone Field -->
                        <div class="form-group mb-3">
                            <label for="phone" class="form-label">Phone (Username)</label>
                            <input type="tel" name="phone" class="form-control" value="{{ $user->phone }}" required>
                        </div>

                        <!-- User Type Field -->
                        <div class="form-group mb-4">
                            <label for="user_type_id" class="form-label">User Type</label>
                            <select name="user_type_id" class="form-control" required>
                                @foreach($userTypes as $type)
                                    <option value="{{ $type->id }}" {{ $user->user_type_id == $type->id ? 'selected' : '' }}>
                                        {{ $type->role_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
