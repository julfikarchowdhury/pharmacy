@extends('admin.layouts.master')

@section('content')
    <div class="d-flex align-items-center justify-content-end">
        <nav aria-label="breadcrumb" class="d-flex align-items-center">
            <ol class="breadcrumb mb-0 bg-transparent">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tips</li>
            </ol>
        </nav>
    </div>

    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Tips</h6>
                <a href="{{ route('tips.create') }}" class="btn btn-primary">Add New Tip</a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select id="filterType" class="form-control">
                            <option value="">All Types</option>
                            <option value="gym">Gym</option>
                            <option value="health">Health</option>
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tipsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Title (EN)</th>
                                <th>Title (BN)</th>
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
        $(document).ready(function() {
            let table = $('#tipsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('tips.index') }}',
                    data: function(d) {
                        d.type = $('#filterType').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'title_en',
                        name: 'title_en'
                    },
                    {
                        data: 'title_bn',
                        name: 'title_bn'
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

            $('#filterType').change(function() {
                table.draw();
            });
        });

        function deleteTip(tipId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this tip!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/tips/' + tipId,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Deleted!', response.message, 'success')
                                    .then(() => {
                                        $('#tipsTable').DataTable().ajax.reload();
                                    });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'There was an issue with the request.', 'error');
                        }
                    });
                }
            });
        }

        function changeStatus(tipId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to change the tip's status?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/tips/change-status/' + tipId,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Updated!', 'The status has been changed.', 'success');
                                $('#tipsTable').DataTable().ajax.reload(); // Reload the DataTable
                            } else {
                                Swal.fire('Failed!', 'The status could not be changed.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
