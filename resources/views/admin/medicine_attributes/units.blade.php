@extends('admin.layouts.master')

@section('content')
<!-- Breadcrumbs -->
<div class="d-flex align-items-center justify-content-end">
    <nav aria-label="breadcrumb" class="d-flex align-items-center">
        <ol class="breadcrumb mb-0 bg-transparent">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Units</li>
        </ol>
    </nav>
</div>
<!-- End Breadcrumbs -->

<div class="container-fluid">
    <!-- Create  Unit Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Create Unit</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('units.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="value">Unit Name</label>
                    <input type="text" id="value" name="value" class="form-control @error('value') is-invalid @enderror"
                        value="{{ old('value') }}" required>
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary float-right">Create</button>
            </form>
        </div>
    </div>

    <!-- List of Units -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Units</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="dataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Unit Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($units as $key => $unit)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $unit->value }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#editUnitModal"
                                    data-id="{{ $unit->id }}" data-value="{{ $unit->value }}">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteUnit({{ $unit->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Unit Modal -->
<div class="modal fade" id="editUnitModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editUnitForm">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Unit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" id="editUnitId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editUnitValue">Unit Name</label>
                        <input type="text" id="editUnitValue" name="value"
                            class="form-control @error('value') is-invalid @enderror" required>
                        <div id="editUnitValueError" class="invalid-feedback"></div>
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
    $('#editUnitModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var unitId = button.data('id');
        var unitValue = button.data('value');
        var modal = $(this);
        modal.find('#editUnitValue').val(unitValue);
        modal.find('#editUnitId').val(unitId);
    });

    $('#editUnitForm').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('_method', 'PUT');
        var actionUrl = '/units/' + $('#editUnitId').val();

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
                    $('#editUnitModal').modal('hide');
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                }
            },
            error: function (xhr) {
                var errors = xhr.responseJSON.errors;
                toastr.error('Something went wrong');
                if (errors.value) {
                    $('#editUnitValueError').text(errors.value[0]);
                    $('#editUnitValue').addClass('is-invalid');
                }
            }
        });
    });

    function deleteUnit(unitId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this unit!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/units/' + unitId,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire('Deleted!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
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