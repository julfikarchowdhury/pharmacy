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
                    <a href="{{ route('tips.index') }}" class="text-primary">Tips</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Add Tip</li>
            </ol>
        </nav>
    </div>
    <!-- end breadcrumbs -->

    <div class="container-fluid">
        <!-- Create Tip Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Create New Tip</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('tips.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Tip Type -->
                        <div class="form-group col-md-6">
                            <label for="type">Tip Type</label>
                            <select id="type" name="type" class="form-control @error('type') is-invalid @enderror"
                                required>
                                <option value="gym" {{ old('type') == 'gym' ? 'selected' : '' }}>Gym</option>
                                <option value="health" {{ old('type') == 'health' ? 'selected' : '' }}>Health</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Status -->
                        <div class="form-group col-md-6">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control @error('status') is-invalid @enderror"
                                required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Tip Title (English) -->
                        <div class="form-group col-md-6">
                            <label for="title_en">Tip Title (English)</label>
                            <input type="text" id="title_en" name="title_en"
                                class="form-control @error('title_en') is-invalid @enderror" value="{{ old('title_en') }}"
                                required>
                            @error('title_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Tip Title (Bangla) -->
                        <div class="form-group col-md-6">
                            <label for="title_bn">Tip Title (Bangla)</label>
                            <input type="text" id="title_bn" name="title_bn"
                                class="form-control @error('title_bn') is-invalid @enderror" value="{{ old('title_bn') }}"
                                required>
                            @error('title_bn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Instruction (English) -->
                        <div class="form-group col-md-6">
                            <label for="instruction_en">Instruction (English)</label>
                            <textarea id="instruction_en" name="instruction_en" class="form-control @error('instruction_en') is-invalid @enderror"
                                required>{{ old('instruction_en') }}</textarea>
                            @error('instruction_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Instruction (Bangla) -->
                        <div class="form-group col-md-6">
                            <label for="instruction_bn">Instruction (Bangla)</label>
                            <textarea id="instruction_bn" name="instruction_bn" class="form-control @error('instruction_bn') is-invalid @enderror"
                                required>{{ old('instruction_bn') }}</textarea>
                            @error('instruction_bn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Image Upload -->
                        <div class="form-group col-md-6">
                            <label for="image">Upload Image</label>
                            <input type="file" id="image" name="image"
                                class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            <div id="image-preview" class="mt-2"></div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Video Upload -->
                        <div class="form-group col-md-6">
                            <label for="video">Upload Video</label>
                            <input type="file" id="video" name="video"
                                class="form-control @error('video') is-invalid @enderror" accept="video/*">
                            <div id="video-preview" class="mt-2"></div>
                            @error('video')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Submit Button -->
                        <div class="form-group col-md-12 text-right">
                            <button type="submit" class="btn btn-primary btn-lg">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#instruction_en').summernote({
                height: 200,
                placeholder: 'Enter instruction in English'
            });

            $('#instruction_bn').summernote({
                height: 200,
                placeholder: 'Enter instruction in Bangla'
            });
            // Image Preview
            $('#image').on('change', function(event) {
                let previewContainer = $('#image-preview');
                previewContainer.empty();
                let file = event.target.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.html(
                            `<img src="${e.target.result}" class="img-thumbnail" width="150">`);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Video Preview
            $('#video').on('change', function(event) {
                let previewContainer = $('#video-preview');
                previewContainer.empty();
                let file = event.target.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.html(
                            `<video controls width="250"><source src="${e.target.result}" type="video/mp4">Your browser does not support the video tag.</video>`
                        );
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection
