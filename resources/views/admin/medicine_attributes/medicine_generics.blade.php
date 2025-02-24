@extends('admin.layouts.master')

@section('content')
<!-- breadcrumbs -->
<div class="d-flex align-items-center justify-content-end">
    <nav aria-label="breadcrumb" class="d-flex align-items-center">
        <ol class="breadcrumb mb-0 bg-transparent">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Medicine Generics</li>
        </ol>
    </nav>
</div>
<!-- end breadcrumbs -->

<div class="container-fluid">
    <!-- Create Medicine Generic Form at the Top -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Create Medicine Generic</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('medicine_generics.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="title_bn">Generic Name (Bangla)</label>
                        <input type="text" id="title_bn" name="title_bn"
                            class="form-control @error('title_bn') is-invalid @enderror" value="{{ old('title_bn') }}"
                            required>
                        @error('title_bn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="title_en">Generic Name (English)</label>
                        <input type="text" id="title_en" name="title_en"
                            class="form-control @error('title_en') is-invalid @enderror" value="{{ old('title_en') }}"
                            required>
                        @error('title_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary float-right">Create</button>
            </form>
        </div>
    </div>

    <!-- List of Medicine Generics -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Medicine Generics</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="medicineGenericsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name (Bangla)</th>
                        <th>Name (English)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Editing Medicine Generic -->
<div class="modal fade" id="editMedicineGenericModal" tabindex="-1" role="dialog"
    aria-labelledby="editMedicineGenericModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editMedicineGenericForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMedicineGenericModalLabel">Edit Medicine Generic</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" id="editGenericId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editGenericNameBn">Generic Name (Bangla)</label>
                        <input type="text" id="editGenericNameBn" name="title_bn"
                            class="form-control @error('title_bn') is-invalid @enderror" required>
                        <div id="editGenericNameBnError" class="invalid-feedback"></div>
                        @error('title_bn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="editGenericNameEn">Generic Name (English)</label>
                        <input type="text" id="editGenericNameEn" name="title_en"
                            class="form-control @error('title_en') is-invalid @enderror" required>
                        <div id="editGenericNameEnError" class="invalid-feedback"></div>
                        @error('title_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
    $(document).ready(function () {
        $('#medicineGenericsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('medicine_generics.index') }}',
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'title_bn',
                name: 'title_bn'
            },
            {
                data: 'title_en',
                name: 'title_en'
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false
            }
            ]
        });
    });
    $('#editMedicineGenericModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var genericId = button.data('id');
        var genericNameBn = button.data('title_bn');
        var genericNameEn = button.data('title_en');

        var modal = $(this);
        modal.find('#editGenericNameBn').val(genericNameBn);
        modal.find('#editGenericNameEn').val(genericNameEn);
        modal.find('#editGenericId').val(genericId);
    });

    $('#editMedicineGenericForm').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('_method', 'PUT');
        var actionUrl = '/medicine_generics/' + $('#editGenericId').val();

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
                    $('#editMedicineGenericModal').modal('hide');
                    $('#medicineGenericsTable').DataTable().ajax.reload(null, false);

                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON.errors;
                toastr.error('Something went wrong');
                if (errors.title_bn) {
                    $('#editGenericNameBnError').text(errors.title_bn[0]);
                    $('#editGenericNameBn').addClass('is-invalid');
                }
                if (errors.title_en) {
                    $('#editGenericNameEnError').text(errors.title_en[0]);
                    $('#editGenericNameEn').addClass('is-invalid');
                }
            }
        });
    });

    function deleteMedicineGeneric(genericId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this generic!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/medicine_generics/' + genericId,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            ).then(() => {
                                $('#medicineGenericsTable').DataTable().ajax.reload(null,
                                    false);
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
</script>
@endsection