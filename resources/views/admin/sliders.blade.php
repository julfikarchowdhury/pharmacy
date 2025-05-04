@extends('admin.layouts.master')

@section('content')
    <!-- breadcrumbs -->
    <div class="d-flex align-items-center justify-content-end">
        <nav aria-label="breadcrumb" class="d-flex align-items-center">
            <ol class="breadcrumb mb-0 bg-transparent">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Sliders</li>
            </ol>
        </nav>
    </div>
    <!-- end breadcrumbs -->

    <div class="container-fluid">
        <!-- Create Slider Form at the Top -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Create Slider</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('sliders.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="title">Slider Title</label>
                            <input type="text" id="title" name="title"
                                class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                                required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="image">Slider Image</label>
                            <input type="file" id="image" name="image"
                                class="form-control @error('image') is-invalid @enderror">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="status">Slider Status</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                <option value="active">Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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

        <!-- List of Sliders -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sliders</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sliders as $key => $slider)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $slider->title }}</td>
                                <td><img src="{{ asset($slider->image) }}" alt="" height="50" width="auto"></td>
                                <td>
                                    <a href="#" class="ml-2" onclick="changeStatus({{ $slider->id }})">
                                        @if ($slider->status === 'active')
                                            <span class="badge bg-success text-white">Active</span>
                                        @else
                                            <span class="badge bg-danger text-white">Inactive</span>
                                        @endif
                                    </a>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#editSliderModal"
                                        data-id="{{ $slider->id }}" data-title="{{ $slider->title }}"
                                        data-image="{{ $slider->image }}" data-status="{{ $slider->status }}">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteSlider({{ $slider->id }})"><i
                                            class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Editing Slider -->
    <div class="modal fade" id="editSliderModal" tabindex="-1" role="dialog" aria-labelledby="editSliderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editSliderForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSliderModalLabel">Edit Slider</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <input type="hidden" id="editSliderId">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="editTitle">Slider Title</label>
                            <input type="text" id="editTitle" name="title"
                                class="form-control @error('title') is-invalid @enderror" required>
                            <div id="editTitleError" class="invalid-feedback"></div>
                        </div>



                        <div class="form-group">
                            <label for="editStatus">Slider Status</label>
                            <select name="status" id="editStatus"
                                class="form-control @error('status') is-invalid @enderror">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="editImage">Slider Image</label>
                            <input type="file" id="editImage" name="image" class="form-control">
                            <div id="editImageError" class="invalid-feedback"></div>
                        </div>

                        <div>
                            <b>Slider Image</b>
                            <hr>
                            <img src="" id="sliderImage" height="100" width="100" alt="">
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
        $('#editSliderModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var sliderId = button.data('id');
            var sliderTitle = button.data('title');
            var sliderImage = button.data('image');
            var sliderStatus = button.data('status');

            var modal = $(this);
            modal.find('#editTitle').val(sliderTitle);
            modal.find('#editSliderId').val(sliderId);
            modal.find('#sliderImage').attr('src', sliderImage);
            modal.find('#editStatus').val(sliderStatus); // Select the status
        });

        $('#editSliderForm').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            formData.append('_method', 'PUT');
            var actionUrl = '/sliders/' + $('#editSliderId').val();

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
                        $('#editSliderModal').modal('hide');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON.errors;
                    toastr.error('Something went wrong');
                    if (errors.title) {
                        $('#editTitleError').text(errors.title[0]);
                        $('#editTitle').addClass('is-invalid');
                    }
                    if (errors.image) {
                        $('#editImageError').text(errors.image[0]);
                        $('#editImage').addClass('is-invalid');
                    }
                }
            });
        });

        function deleteSlider(sliderId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this slider!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/sliders/' + sliderId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            toastr.success(response.message);
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        },
                        error: function (xhr) {
                            toastr.error('Something went wrong!');
                        }
                    });
                }
            });
        }

        function changeStatus(sliderId) {
            // Display SweetAlert2 confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to change the slider status?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Make an AJAX request to change the status
                    $.ajax({
                        url: '/sliders/change-status/' + sliderId,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function (response) {
                            if (response.success) {
                                // Update the status in the DataTable
                                Swal.fire('Updated!', 'The status has been changed.', 'success');
                                location.reload();
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