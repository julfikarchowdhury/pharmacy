@extends('admin.layouts.master')

@section('content')
<!-- breadcrumbs -->
<div class="d-flex align-items-center justify-content-end">
    <nav aria-label="breadcrumb" class="d-flex align-items-center">
        <ol class="breadcrumb mb-0 bg-transparent">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Categories</li>
        </ol>
    </nav>
</div>
<!-- end breadcrumbs -->

<div class="container-fluid">
    <!-- Create Category Form at the Top -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Create Category</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="name_bn">Category Name (Bangla)</label>
                        <input type="text" id="name_bn" name="name_bn"
                            class="form-control @error('name_bn') is-invalid @enderror" value="{{ old('name_bn') }}"
                            required>
                        @error('name_bn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="name_en">Category Name (English)</label>
                        <input type="text" id="name_en" name="name_en"
                            class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en') }}"
                            required>
                        @error('name_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="icon">Category Icon</label>
                        <input type="file" id="icon" name="icon"
                            class="form-control @error('icon') is-invalid @enderror">
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="status">Category Status</label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                            <option value="active">Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary float-right">Create</button>
            </form>
        </div>
    </div>

    <!-- List of Categories -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Categories</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="dataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name (Bangla)</th>
                        <th>Name (English)</th>
                        <th>Icon</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $key => $category)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $category->name_bn }}</td>
                            <td>{{ $category->name_en }}</td>
                            <td><img src="{{ asset($category->icon) }}" alt="" height="50" width="auto">
                            </td>
                            <td>
                                <a href="#" class="ml-2" onclick="changeStatus({{ $category->id }})">
                                    @if ($category->status === 'active')
                                        <span class="badge bg-success text-white">Active</span>
                                    @else
                                        <span class="badge bg-danger text-white">Inactive</span>
                                    @endif
                                </a>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#editCategoryModal"
                                    data-id="{{ $category->id }}" data-name_bn="{{ $category->name_bn }}"
                                    data-name_en="{{ $category->name_en }}" data-icon="{{ $category->icon }}"
                                    data-status="{{ $category->status }}"><i class="fas fa-pen"></i></button>
                                <button class="btn btn-danger btn-sm" onclick="deleteCategory({{ $category->id }})"><i
                                        class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Editing Category -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editCategoryForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" id="editCategoryId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editCategoryNameBn">Category Name (Bangla)</label>
                        <input type="text" id="editCategoryNameBn" name="name_bn"
                            class="form-control @error('name_bn') is-invalid @enderror" required>
                        <div id="editCategoryNameBnError" class="invalid-feedback"></div>
                        @error('name_bn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="editCategoryNameEn">Category Name (English)</label>
                        <input type="text" id="editCategoryNameEn" name="name_en"
                            class="form-control @error('name_en') is-invalid @enderror" required>
                        <div id="editCategoryNameEnError" class="invalid-feedback"></div>
                        @error('name_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="status">Category Status</label>
                        <select name="status" id="editStatus"
                            class="form-control @error('status') is-invalid @enderror">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="editIcon">Category Icon</label>
                        <input type="file" id="editIcon" name="icon"
                            class="form-control @error('icon') is-invalid @enderror">
                        <div id="editIconError" class="invalid-feedback"></div>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <b>Category Icon</b>
                        <hr>
                        <img src="" id="categoryIcon" height="100" width="100" alt="">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#editCategoryModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var categoryId = button.data('id');
        var categoryNameBn = button.data('name_bn');
        var categoryNameEn = button.data('name_en');
        var categoryIcon = button.data('icon');
        var categoryStatus = button.data('status');

        var modal = $(this);
        modal.find('#editCategoryNameBn').val(categoryNameBn);
        modal.find('#editCategoryNameEn').val(categoryNameEn);
        modal.find('#editCategoryId').val(categoryId);
        modal.find('#categoryIcon').attr('src', categoryIcon);
        modal.find('#editStatus').val(categoryStatus); // Select the status

    });

    $('#editCategoryForm').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('_method', 'PUT');
        var actionUrl = '/categories/' + $('#editCategoryId').val();

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    $('#editCategoryModal').modal('hide');
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON.errors;
                toastr.error('Something went wrong');
                if (errors.name_bn) {
                    $('#editCategoryNameBnError').text(errors.name_bn[0]);
                    $('#editCategoryNameBn').addClass('is-invalid');
                }
                if (errors.name_en) {
                    $('#editCategoryNameEnError').text(errors.name_en[0]);
                    $('#editCategoryNameEn').addClass('is-invalid');
                }
                if (errors.icon) {
                    $('#editIconError').text(errors.icon[0]);
                    $('#editIcon').addClass('is-invalid');
                }
            }
        });
    });


    function deleteCategory(categoryId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this category!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/categories/' + categoryId,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            ).then(() => {
                                location.reload(); // Reload the page
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = 'An error occurred. Please try again.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire(
                            'Error!',
                            errorMessage,
                            'error'
                        );
                    }
                });
            }
        });
    }

    function changeStatus(categoryId) {
        // Display SweetAlert2 confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to change the category status?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, change it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Make an AJAX request to change the status
                $.ajax({
                    url: '/categories/change-status/' + categoryId,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function (response) {
                        if (response.success) {
                            // Update the status in the DataTable
                            Swal.fire('Updated!', 'The status has been changed.', 'success');
                            location.reload();
                            // $('#dataTable').DataTable().ajax.reload(); // Reload the DataTable
                        } else {
                            Swal.fire('Failed!', 'The status could not be changed.', 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    }
</script>
@endsection