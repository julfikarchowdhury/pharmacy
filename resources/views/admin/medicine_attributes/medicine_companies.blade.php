@extends('admin.layouts.master')

@section('content')
<!-- breadcrumbs -->
<div class="d-flex align-items-center justify-content-end">
    <nav aria-label="breadcrumb" class="d-flex align-items-center">
        <ol class="breadcrumb mb-0 bg-transparent">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Medicine Companies</li>
        </ol>
    </nav>
</div>
<!-- end breadcrumbs -->

<div class="container-fluid">
    <!-- Create Medicine Company Form at the Top -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Create Medicine Company</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('medicine_companies.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="name_bn">Company Name (Bangla)</label>
                        <input type="text" id="name_bn" name="name_bn"
                            class="form-control @error('name_bn') is-invalid @enderror" value="{{ old('name_bn') }}"
                            required>
                        @error('name_bn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="name_en">Company Name (English)</label>
                        <input type="text" id="name_en" name="name_en"
                            class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en') }}"
                            required>
                        @error('name_en')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary float-right">Create</button>
            </form>
        </div>
    </div>

    <!-- List of Medicine Companies -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Medicine Companies</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="medicineCompaniesTable">
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

<!-- Modal for Editing Medicine Company -->
<div class="modal fade" id="editMedicineCompanyModal" tabindex="-1" role="dialog"
    aria-labelledby="editMedicineCompanyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editMedicineCompanyForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMedicineCompanyModalLabel">Edit Medicine Company</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" id="editCompanyId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editCompanyNameBn">Company Name (Bangla)</label>
                        <input type="text" id="editCompanyNameBn" name="name_bn"
                            class="form-control @error('name_bn') is-invalid @enderror" required>
                        <div id="editCompanyNameBnError" class="invalid-feedback"></div>
                        @error('name_bn')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="editCompanyNameEn">Company Name (English)</label>
                        <input type="text" id="editCompanyNameEn" name="name_en"
                            class="form-control @error('name_en') is-invalid @enderror" required>
                        <div id="editCompanyNameEnError" class="invalid-feedback"></div>
                        @error('name_en')
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
        $('#medicineCompaniesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('medicine_companies.index') }}',
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'name_bn',
                name: 'name_bn'
            },
            {
                data: 'name_en',
                name: 'name_en'
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
    $('#editMedicineCompanyModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var companyId = button.data('id');
        var companyNameBn = button.data('name_bn');
        var companyNameEn = button.data('name_en');

        var modal = $(this);
        modal.find('#editCompanyNameBn').val(companyNameBn);
        modal.find('#editCompanyNameEn').val(companyNameEn);
        modal.find('#editCompanyId').val(companyId);
    });

    $('#editMedicineCompanyForm').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('_method', 'PUT');
        var actionUrl = '/medicine_companies/' + $('#editCompanyId').val();

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
                    $('#editMedicineCompanyModal').modal('hide');
                    $('#medicineCompaniesTable').DataTable().ajax.reload(null, false);

                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON.errors;
                toastr.error('Something went wrong');
                if (errors.name_bn) {
                    $('#editCompanyNameBnError').text(errors.name_bn[0]);
                    $('#editCompanyNameBn').addClass('is-invalid');
                }
                if (errors.name_en) {
                    $('#editCompanyNameEnError').text(errors.name_en[0]);
                    $('#editCompanyNameEn').addClass('is-invalid');
                }
            }
        });
    });

    function deleteMedicineCompany(companyId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this company!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/medicine_companies/' + companyId,
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
                                $('#medicineCompaniesTable').DataTable().ajax.reload(null,
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