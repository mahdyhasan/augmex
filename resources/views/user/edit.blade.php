@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit User</h1>
    <form action="{{ route('user.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>
          <div class="form-group">
            <label for="phone">Phone</label>
            <input type="phone" name="phone" class="form-control" value="{{ $user-> phone }}" required>
        </div>
        <div class="form-group">
            <label for="user_type_id">User Type</label>
            <select name="user_type_id" class="form-control" required>
                @foreach($userTypes as $type)
                    <option value="{{ $type->id }}" {{ $user->user_type_id == $type->id ? 'selected' : '' }}>
                        {{ $type->role_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection