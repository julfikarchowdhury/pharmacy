@extends('admin.layouts.master')

@section('content')
    <!-- breadcrumbs -->
    <div class="d-flex align-items-center justify-content-end">
        <nav aria-label="breadcrumb" class="d-flex align-items-center">
            <ol class="breadcrumb mb-0 bg-transparent">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Settings</li>
            </ol>
        </nav>
    </div>
    <!-- end bradcrumbs -->
    <div class="container-fluid">
        <!-- Settings Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Update Settings</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- App Name -->
                        <div class="form-group col-md-6">
                            <label for="app_name">App Name</label>
                            <input type="text" id="app_name" name="app_name" class="form-control"
                                value="{{ old('app_name', setting()->app_name) }}" required placeholder="Enter App Name">
                            @error('app_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- App Logo -->
                        <div class="form-group col-md-6">
                            <label for="logo">App Logo</label>
                            <input type="file" id="logo" name="logo" class="form-control" accept="image/*"
                                placeholder="Upload App Logo">
                            @error('logo')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- points_conversion -->
                        <div class="form-group col-md-6">
                            <label for="points_conversion">Point Conversion</label>
                            <input type="number" step="0.01" id="points_conversion" name="points_conversion"
                                class="form-control" value="{{ old('points_conversion', setting()->points_conversion) }}"
                                placeholder="Enter Point Conversion">
                            @error('points_conversion')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- points_conversion -->
                        <div class="form-group col-md-6">
                            <label for="delivery_charge_rate">Delivery Charge Rate</label>
                            <input type="number" step="0.01" id="delivery_charge_rate" name="delivery_charge_rate"
                                class="form-control"
                                value="{{ old('delivery_charge_rate', setting()->delivery_charge_rate) }}"
                                placeholder="Enter Delivery Charge Rate Per Kilo">
                            @error('delivery_charge_rate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- points_conversion -->
                        <div class="form-group col-md-6">
                            <label for="tax_percentage">TAX Percentage</label>
                            <input type="number" step="0.01" id="tax_percentage" name="tax_percentage" class="form-control"
                                value="{{ old('tax_percentage', setting()->tax_percentage) }}"
                                placeholder="Enter tax percentage">
                            @error('tax_percentage')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Currency -->
                        <div class="form-group col-md-6">
                            <label for="currency_code">Currency Code</label>
                            <input type="text" id="currency_code" name="currency_code" class="form-control"
                                value="{{ old('currency_code', setting()->currency_code) }}" required
                                placeholder="Enter Currency Code">
                            @error('currency_code')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Currency Icon -->
                        <div class="form-group col-md-6">
                            <label for="currency_icon">Currency Icon</label>
                            <input type="text" id="currency_icon" name="currency_icon" class="form-control"
                                value="{{ old('currency_icon', setting()->currency_icon) }}" required
                                placeholder="Enter Currency Icon">
                            @error('currency_icon')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group col-md-12 text-right">
                            <button type="submit" class="btn btn-primary">Update Settings</button>
                        </div>
                    </div>
                </form>


            </div>
        </div>
    </div>
@endsection