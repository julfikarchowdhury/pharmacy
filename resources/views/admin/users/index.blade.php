@extends('admin.layouts.master')

@section('content')
<div class="d-flex align-items-center justify-content-end">
    <nav aria-label="breadcrumb" class="d-flex align-items-center">
        <ol class="breadcrumb mb-0 bg-transparent">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Users</li>
        </ol>
    </nav>
</div>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Users</h6>
            {{-- <a href="{{ route('users.create') }}" class="btn btn-primary">Add New User</a> --}}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        let table = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('users.index') }}',
                method: 'GET'
            },
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'image',
                name: 'image'
            }, {
                data: 'phone',
                name: 'phone'
            },
            {
                data: 'status',
                name: 'status',
                orderable: false,
                searchable: false
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false
            }
            ]
        });

        $('#filterType').change(function () {
            table.draw();
        });
    });

    function deleteUser(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this user!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/users/' + userId,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire('Deleted!', response.message, 'success')
                                .then(() => {
                                    $('#usersTable').DataTable().ajax.reload();
                                });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error!', 'There was an issue with the request.', 'error');
                    }
                });
            }
        });
    }

    function changeStatus(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to change the user's status?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, change it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/users/change-status/' + userId,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Updated!', 'The status has been changed.', 'success');
                            $('#usersTable').DataTable().ajax.reload(); // Reload the DataTable
                        } else {
                            Swal.fire('Failed!', 'The status could not be changed.', 'error');
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