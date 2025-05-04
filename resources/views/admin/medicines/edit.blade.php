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
                <li class="breadcrumb-item active" aria-current="page">Edit Medicine</li>
            </ol>
        </nav>
    </div>
    <!-- end breadcrumbs -->

    <div class="container-fluid">
        <!-- Edit Medicine Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Medicine</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('medicines.update', $medicine->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Medicine Name (English) -->
                        <div class="form-group col-md-6">
                            <label for="name_en">Medicine Name (English)</label>
                            <input type="text" id="name_en" name="name_en"
                                class="form-control @error('name_en') is-invalid @enderror"
                                value="{{ old('name_en', $medicine->name_en) }}" required>
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Medicine Name (Bangla) -->
                        <div class="form-group col-md-6">
                            <label for="name_bn">Medicine Name (Bangla)</label>
                            <input type="text" id="name_bn" name="name_bn"
                                class="form-control @error('name_bn') is-invalid @enderror"
                                value="{{ old('name_bn', $medicine->name_bn) }}" required>
                            @error('name_bn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description (English) -->
                        <div class="form-group col-md-6">
                            <label for="description_en">Description (English)</label>
                            <textarea id="description_en" name="description_en" class="form-control @error('description_en') is-invalid @enderror"
                                required>{{ old('description_en', $medicine->description_en) }}</textarea>
                            @error('description_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description (Bangla) -->
                        <div class="form-group col-md-6">
                            <label for="description_bn">Description (Bangla)</label>
                            <textarea id="description_bn" name="description_bn" class="form-control @error('description_bn') is-invalid @enderror"
                                required>{{ old('description_bn', $medicine->description_bn) }}</textarea>
                            @error('description_bn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category Dropdown -->
                        <div class="form-group col-md-6">
                            <label for="category_id">Category</label>
                            <select id="category_id" name="category_id" class="form-control select2" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $medicine->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Medicine Company Dropdown -->
                        <div class="form-group col-md-6">
                            <label for="medicine_company_id">Medicine Company</label>
                            <select id="medicine_company_id" name="medicine_company_id" class="form-control select2"
                                required>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}"
                                        {{ $medicine->medicine_company_id == $company->id ? 'selected' : '' }}>
                                        {{ $company->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Medicine Generic Dropdown (Select2) -->
                        <div class="form-group col-md-6">
                            <label for="medicine_generic_id">Medicine Generic</label>
                            <select id="medicine_generic_id" name="medicine_generic_id" class="form-control select2"
                                required>
                                <option value="">Select Generic</option>
                                @foreach ($generics as $generic)
                                    <option value="{{ $generic->id }}"
                                        {{ $medicine->medicine_generic_id == $generic->id ? 'selected' : '' }}>
                                        {{ $generic->title_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                         {{-- concentration --}}
                         <div class="form-group col-md-6">
                            <label for="concentration_id">Medicine Concentration</label>
                            <select id="concentration_id" name="concentration_id" class="form-control select2"
                                required>
                                <option value="">Select Concentration</option>
                                @foreach ($concentrations as $concentration)
                                    <option value="{{ $concentration->id }}"
                                        {{ $medicine->concentration_id == $concentration->id ? 'selected' : '' }}>
                                        {{ $concentration->value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Unit Price -->
                        <div class="form-group col-md-6">
                            <label for="unit_price">Unit Price</label>
                            <input type="text" id="unit_price" name="unit_price"
                                class="form-control @error('unit_price') is-invalid @enderror"
                                value="{{ old('unit_price', $medicine->unit_price) }}" required
                                placeholder="Enter unit price">
                            @error('unit_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Strip Price -->
                        <div class="form-group col-md-6">
                            <label for="strip_price">Strip Price</label>
                            <input type="text" id="strip_price" name="strip_price"
                                class="form-control @error('strip_price') is-invalid @enderror"
                                value="{{ old('strip_price', $medicine->strip_price) }}" required
                                placeholder="Enter strip price">
                            @error('strip_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-group col-md-6">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="active"
                                    {{ old('status', $medicine->status) == 'active' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="inactive"
                                    {{ old('status', $medicine->status) == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="units">Select Units</label>
                            <select id="units" name="units[]" class="form-control" multiple="multiple">
                                @foreach (units() as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ in_array($unit->id, old('units', $medicine->units->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $unit->value_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <!-- Image Upload -->
                        <div class="form-group col-md-12">
                            <label for="images">Upload New Images</label>
                            <input type="file" id="images" name="images[]" class="form-control" multiple
                                accept="image/*">

                            <!-- Hidden input to store removed existing image IDs -->
                            <input type="hidden" id="removed_images" name="removed_images" value="[]">

                            <div id="image-previews" class="mt-2">
                                @foreach ($medicine->images as $image)
                                    <div class="image-preview mt-2" data-image-id="{{ $image->id }}">
                                        <img src="{{ asset($image->src) }}" class="img-thumbnail" width="100">
                                        <button type="button"
                                            class="btn btn-danger btn-sm mt-2 remove-existing-image">Remove</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>



                        <!-- Submit Button -->
                        <div class="form-group col-md-12 text-right">
                            <button type="submit" class="btn btn-primary btn-lg">Update</button>
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
            $('.select2').select2();
            $('#description_en, #description_bn').summernote({
                height: 200
            });
            $('#units').select2({
                placeholder: "Select units",
                allowClear: true,
                width: '100%'
            });
        });
        $(document).ready(function() {
            var removedImages = [];

            function updateRemovedImagesField() {
                $('#removed_images').val(JSON.stringify(removedImages));
            }

            const $imagesInput = $('#images');
            const $imagePreviewsContainer = $('#image-previews');

            $imagesInput.on('change', function(event) {
                const files = event.target.files;

                $.each(files, function(index, file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const previewDiv = $('<div>', {
                            class: 'image-preview mt-2'
                        });
                        const img = $('<img>', {
                            src: e.target.result,
                            class: 'img-thumbnail',
                            width: 100
                        });
                        const removeButton = $('<button>', {
                            type: 'button',
                            class: 'btn btn-danger btn-sm mt-2 remove-new-image',
                            text: 'Remove'
                        });

                        previewDiv.append(img, removeButton);

                        $imagePreviewsContainer.append(previewDiv);

                        removeButton.on('click', function() {
                            previewDiv.remove();
                            removeFileFromInput(file);
                        });
                    };

                    reader.readAsDataURL(file);
                });
            });

            function removeFileFromInput(file) {
                const dataTransfer = new DataTransfer();
                const files = $imagesInput[0].files;

                $.each(files, function(index, existingFile) {
                    if (existingFile !== file) {
                        dataTransfer.items.add(existingFile);
                    }
                });

                $imagesInput[0].files = dataTransfer.files;
            }

            $(document).on('click', '.remove-existing-image', function() {
                const $preview = $(this).closest('.image-preview');
                const imageId = $preview.data('image-id');

                if (removedImages.indexOf(imageId) === -1) {
                    removedImages.push(imageId);
                }
                updateRemovedImagesField();

                $preview.remove();
            });
        });
    </script>
@endsection
