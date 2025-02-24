@extends('admin.layouts.master')

@section('content')
<!-- breadcrumbs -->
<div class="d-flex align-items-center justify-content-end">
    <nav aria-label="breadcrumb" class="d-flex align-items-center">
        <ol class="breadcrumb mb-0 bg-transparent">
            <li class="breadcrumb-item">
                <a href="{{route('admin.dashboard')}}" class="text-primary">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">User Profile</li>
        </ol>
    </nav>
</div>
<!-- end bradcrumbs -->
<div class="container-fluid">
    <!-- DataTales Example -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Profile</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="d-flex justify-content-center">
                            <img src="{{asset(auth()->user()->image ?? 'admin/img/undraw_profile.svg')}}" alt=""
                                style="height:200px;width:200px">
                        </div>
                        <!-- name -->
                        <div class="form-group ">
                            <label for="name">name</label>
                            <input type="text" id="name" name="name" class="form-control"
                                value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Image Upload -->
                        <div class="form-group ">
                            <label for="image">Profile Image</label>
                            <input type="file" id="image" name="image" class="form-control">
                            @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group ">
                            <button type="submit" class="btn btn-primary float-right">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Change Password</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        <div class="form-group ">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control"
                                required>
                            @error('current_password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group ">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group ">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" required>
                        </div>
                        <!-- Submit Button -->
                        <div class="form-group ">
                            <button type="submit" class="btn btn-primary float-right">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection