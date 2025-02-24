@extends('admin.layouts.master')

@section('content')
    <!-- breadcrumbs -->
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-2 text-gray-800">Requested Medicines</h1>
        <nav aria-label="breadcrumb" class="d-flex align-items-center">
            <ol class="breadcrumb mb-0 bg-transparent">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Requested Medicines</li>
            </ol>
        </nav>
    </div>

    <!-- end bradcrumbs -->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Requested Medicines Table</h6>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="requestedMedicinesTable">
                    <thead>
                        <tr>
                            <th>Requested By</th>
                            <th>Medicine Name</th>
                            <th>Category Name</th>
                            <th>Company Name</th>
                            <th>Generic Name</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#requestedMedicinesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('requested_medicines') }}',
                    method: 'GET'
                },
                columns: [{
                    data: 'user.name',
                    name: 'user.name'
                }, {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'category',
                    name: 'category'
                }, {
                    data: 'company',
                    name: 'company'
                }, {
                    data: 'generic',
                    name: 'generic'
                }, {
                    data: 'image',
                    name: 'image'
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data) {
                        return data;
                    }
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        return data;
                    }
                }
                ]
            });
        });

        function deleteMedicine(medicineId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this medicine!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/medicines/' + medicineId,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire('Deleted!', response.message, 'success')
                                    .then(() => {
                                        $('#requestedMedicinesTable').DataTable().ajax.reload();
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

        function changeStatus(medicineId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to change the medicine's status?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/medicines/change-status/' + medicineId,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire('Updated!', 'The status has been changed.', 'success');
                                $('#requestedMedicinesTable').DataTable().ajax.reload();
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