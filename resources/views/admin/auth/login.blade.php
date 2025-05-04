@extends('admin.auth.master')
@section('content')
    <!-- Outer Row -->
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-xl-5 col-lg-5 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="p-md-5 p-3">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                        </div>
                        <form class="user" action="{{ route('admin.login.post') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <input type="email"
                                    class="form-control form-control-user @error('email') is-invalid @enderror"
                                    id="email" aria-describedby="emailHelp" name="email" placeholder="Enter email..."
                                    value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input type="password"
                                    class="form-control form-control-user @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="Password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button class="btn btn-primary btn-user btn-block" type="submit">
                                Login
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
