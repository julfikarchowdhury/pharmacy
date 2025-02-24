@extends('admin.layouts.master')

@section('content')
    <!-- breadcrumbs -->
    <div class="d-flex align-items-center justify-content-end">
        <nav aria-label="breadcrumb" class="d-flex align-items-center">
            <ol class="breadcrumb mb-0 bg-transparent">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('medicines.index') }}" class="text-primary">Medicines</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Medicine Details</li>
            </ol>
        </nav>
    </div>
    <!-- end breadcrumbs -->

    <div class="container-fluid">
        <!-- Medicine Details Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Medicine Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Medicine Name (English) -->
                    <div class="form-group col-md-6">
                        <label for="name_en">Medicine Name (English)</label>
                        <p>{{ $medicine->name_en }}</p>
                    </div>

                    <!-- Medicine Name (Bangla) -->
                    <div class="form-group col-md-6">
                        <label for="name_bn">Medicine Name (Bangla)</label>
                        <p>{{ $medicine->name_bn }}</p>
                    </div>

                    <!-- Description (English) -->
                    <div class="form-group col-md-6">
                        <label for="description_en">Description (English)</label>
                        <p>{{ $medicine->description_en }}</p>
                    </div>

                    <!-- Description (Bangla) -->
                    <div class="form-group col-md-6">
                        <label for="description_bn">Description (Bangla)</label>
                        <p>{{ $medicine->description_bn }}</p>
                    </div>

                    <!-- Category -->
                    <div class="form-group col-md-6">
                        <label for="category_id">Category</label>
                        <p>{{ $medicine->category->name_en }}</p>
                    </div>

                    <!-- Medicine Company -->
                    <div class="form-group col-md-6">
                        <label for="medicine_company_id">Medicine Company</label>
                        <p>{{ $medicine->company->name_en }}</p>
                    </div>

                    <!-- Medicine Generic -->
                    <div class="form-group col-md-6">
                        <label for="medicine_generic_id">Medicine Generic</label>
                        <p>{{ $medicine->generic->title_en }}</p>
                    </div>

                    <!-- Medicine Concentration -->
                    <div class="form-group col-md-6">
                        <label for="concentration_id">Medicine Concentration</label>
                        <p>{{ $medicine->concentration->value }}</p>
                    </div>

                    <!-- Unit Price -->
                    <div class="form-group col-md-6">
                        <label for="unit_price">Unit Price</label>
                        <p>{{ $medicine->unit_price }}</p>
                    </div>

                    <!-- Strip Price -->
                    <div class="form-group col-md-6">
                        <label for="strip_price">Strip Price</label>
                        <p>{{ $medicine->strip_price }}</p>
                    </div>

                    <!-- Status -->
                    <div class="form-group col-md-6">
                        <label for="status">Status</label>
                        <p>{{ ucfirst($medicine->status) }}</p>
                    </div>

                    <!-- Requested By (if available) -->
                    @if($requested_by)
                        <div class="form-group col-md-6">
                            <label for="requested_by">Requested By</label>
                            <p>{{ $requested_by }}</p>
                        </div>
                    @endif

                    <!-- Images -->
                    <div class="form-group col-md-12">
                        <label for="images">Images</label>
                        <div id="image-previews">
                            @foreach ($medicineImages as $image)
                                <div class="image-preview mt-2">
                                    <img src="{{ asset($image->src) }}" class="img-thumbnail" width="100">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="form-group col-md-12 text-right">
                    <a href="{{ route('medicines.index') }}" class="btn btn-secondary btn-lg">Back to List</a>
                </div>
            </div>
        </div>
    </div>
@endsection