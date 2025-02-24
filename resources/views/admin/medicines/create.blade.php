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
                <li class="breadcrumb-item active" aria-current="page">Add Medicine</li>
            </ol>
        </nav>
    </div>
    <!-- end bradcrumbs -->

    <div class="container-fluid">
        <!-- Create Medicine Form -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Create New Medicine</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('medicines.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Medicine Name (English) -->
                        <div class="form-group col-md-6">
                            <label for="name_en">Medicine Name (English)</label>
                            <input type="text" id="name_en" name="name_en"
                                class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en') }}"
                                required placeholder="Enter medicine name in English">
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Medicine Name (Bangla) -->
                        <div class="form-group col-md-6">
                            <label for="name_bn">Medicine Name (Bangla)</label>
                            <input type="text" id="name_bn" name="name_bn"
                                class="form-control @error('name_bn') is-invalid @enderror" value="{{ old('name_bn') }}"
                                required placeholder="Enter medicine name in Bangla">
                            @error('name_bn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description (English) -->
                        <div class="form-group col-md-6">
                            <label for="description_en">Description (English)</label>
                            <textarea id="description_en" name="description_en" class="form-control @error('description_en') is-invalid @enderror"
                                required placeholder="Enter description in English">{{ old('description_en') }}</textarea>
                            @error('description_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description (Bangla) -->
                        <div class="form-group col-md-6">
                            <label for="description_bn">Description (Bangla)</label>
                            <textarea id="description_bn" name="description_bn" class="form-control @error('description_bn') is-invalid @enderror"
                                required placeholder="Enter description in Bangla">{{ old('description_bn') }}</textarea>
                            @error('description_bn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category Dropdown (Select2) -->
                        <div class="form-group col-md-6">
                            <label for="category_id">Category</label>
                            <select id="category_id" name="category_id" class="form-control select2" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Company Dropdown (Select2) -->
                        <div class="form-group col-md-6">
                            <label for="medicine_company_id">Medicine Company</label>
                            <select id="medicine_company_id" name="medicine_company_id" class="form-control select2"
                                required>
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}"
                                        {{ old('medicine_company_id') == $company->id ? 'selected' : '' }}>
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
                                        {{ old('medicine_generic_id') == $generic->id ? 'selected' : '' }}>
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
                                        {{ old('concentration_id') == $concentration->id ? 'selected' : '' }}>
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
                                value="{{ old('unit_price') }}" required placeholder="Enter unit price">
                            @error('unit_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Strip Price -->
                        <div class="form-group col-md-6">
                            <label for="strip_price">Strip Price</label>
                            <input type="text" id="strip_price" name="strip_price"
                                class="form-control @error('strip_price') is-invalid @enderror"
                                value="{{ old('strip_price') }}" required placeholder="Enter strip price">
                            @error('strip_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-group col-md-6">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="categories">Select Units</label>
                            <select id="units" name="units[]" class="form-control" multiple="multiple">
                                @foreach (units() as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ in_array($unit->id, old('units', [])) ? 'selected' : '' }}>
                                        {{ $unit->value_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Image Upload (Multiple) -->
                        <div class="form-group col-md-12">
                            <label for="images">Upload Images</label>
                            <input type="file" id="images" name="images[]" class="form-control" multiple
                                accept="image/*">
                            <div id="image-previews" class="mt-2">
                            </div>
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
            let selectedFiles = [];

            $('.select2').select2();
            $('#units').select2({
                placeholder: "Select units",
                allowClear: true,
                width: '100%'
            });

            $('#description_en').summernote({
                height: 200,
                placeholder: 'Enter description in English'
            });

            $('#description_bn').summernote({
                height: 200,
                placeholder: 'Enter description in Bangla'
            });

            $('#images').on('change', function() {
                let files = Array.from(this.files);
                let previewContainer = $('#image-previews');

                files.forEach(file => selectedFiles.push(file));

                previewContainer.empty();

                selectedFiles.forEach((file, index) => {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.append(`
                    <div class="image-preview mt-2" data-index="${index}">
                        <img src="${e.target.result}" class="img-thumbnail" width="100" />
                        <button type="button" class="btn btn-danger btn-sm mt-2 remove-image" data-index="${index}">Remove</button>
                    </div>
                `);
                    };
                    reader.readAsDataURL(file);
                });

                updateFileInput();
            });

            $(document).on('click', '.remove-image', function() {
                let index = $(this).data('index');

                selectedFiles.splice(index, 1);

                $('#image-previews').empty();
                selectedFiles.forEach((file, newIndex) => {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#image-previews').append(`
                    <div class="image-preview mt-2" data-index="${newIndex}">
                        <img src="${e.target.result}" class="img-thumbnail" width="100" />
                        <button type="button" class="btn btn-danger btn-sm mt-2 remove-image" data-index="${newIndex}">Remove</button>
                    </div>
                `);
                    };
                    reader.readAsDataURL(file);
                });

                updateFileInput();
            });

            function updateFileInput() {
                let dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => dataTransfer.items.add(file));
                document.getElementById('images').files = dataTransfer.files;
            }
        });
    </script>
@endsection
