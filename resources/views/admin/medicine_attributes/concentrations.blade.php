@extends('admin.layouts.master')

@section('content')
<!-- breadcrumbs -->
<div class="d-flex align-items-center justify-content-end">
    <nav aria-label="breadcrumb" class="d-flex align-items-center">
        <ol class="breadcrumb mb-0 bg-transparent">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Concentrations</li>
        </ol>
    </nav>
</div>
<!-- end breadcrumbs -->

<div class="container-fluid">
    <!-- Create Medicine Concentration Form at the Top -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Create Concentration</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('concentrations.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="title_bn">Concentration Value</label>
                        <input type="text" id="value" name="value"
                            class="form-control @error('value') is-invalid @enderror" value="{{ old('value') }}"
                            required>
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary float-right">Create</button>
            </form>
        </div>
    </div>

    <!-- List of Concentrations -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Concentrations</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="concentrationsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Editing Medicine concentration -->
<div class="modal fade" id="editConcentrationModal" tabindex="-1" role="dialog"
    aria-labelledby="editConcentrationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editConcentrationForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editConcentrationModalLabel">Edit Concentration</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" id="editconcentrationsId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editConcentrationValue">Concentration Value</label>
                        <input type="text" id="editConcentrationValue" name="value"
                            class="form-control @error('value') is-invalid @enderror">
                        <div id="editConcentrationValueError" class="invalid-feedback"></div>
                        @error('value')
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
        $('#concentrationsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('concentrations.index') }}',
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'value',
                name: 'value'
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
    $('#editConcentrationModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var concentrationsId = button.data('id');
        var concentrationValue = button.data('value');

        var modal = $(this);
        modal.find('#editConcentrationValue').val(concentrationValue);
        modal.find('#editconcentrationsId').val(concentrationsId);
    });

    $('#editConcentrationForm').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('_method', 'PUT');
        var actionUrl = '/concentrations/' + $('#editconcentrationsId').val();

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
                    $('#editConcentrationModal').modal('hide');
                    $('#concentrationsTable').DataTable().ajax.reload(null, false);

                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON.errors;
                toastr.error('Something went wrong');
                if (errors.value) {
                    $('#editConcentrationValueError').text(errors.value[0]);
                    $('#editConcentrationValue').addClass('is-invalid');
                }
            }
        });
    });

    function deleteConcentration(concentrationsId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this concentration!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/concentrations/' + concentrationsId,
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
                                $('#concentrationsTable').DataTable().ajax.reload(null,
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