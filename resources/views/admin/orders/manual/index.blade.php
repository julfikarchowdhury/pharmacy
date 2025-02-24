@extends('admin.layouts.master')

@section('content')
    <!-- breadcrumbs -->
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-2 text-gray-800">Orders</h1>
        <nav aria-label="breadcrumb" class="d-flex align-items-center">
            <ol class="breadcrumb mb-0 bg-transparent">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Orders</li>
            </ol>
        </nav>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Orders Table</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="ordersTable">
                    <thead>
                        <tr>
                            <th>Tracking Id</th>
                            <th>Pharmacy Name</th>
                            <th>Customer Name</th>
                            <th>Total Amount</th>
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
            $('#ordersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('orders.index') }}',
                    method: 'GET'
                },
                columns: [
                    { data: 'tracking_id', name: 'tracking_id' },
                    { data: 'pharmacy_name', name: 'pharmacy_name' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'total', name: 'total' },
                    {
                        data: 'status',
                        name: 'status',

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

            // Change order status
            $(document).on('change', '.order-status', function () {
                var orderId = $(this).data('order-id');
                var status = $(this).val();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to update the order status?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/orders/change-status/' + orderId,
                            method: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                status: status
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire('Updated!', 'Order status has been updated.', 'success');
                                    $('#ordersTable').DataTable().ajax.reload();
                                } else {
                                    Swal.fire('Failed!', 'Order status could not be updated.', 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });
        });

        function deleteOrder(orderId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this order!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/orders/' + orderId,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire('Deleted!', response.message, 'success')
                                    .then(() => {
                                        $('#ordersTable').DataTable().ajax.reload();
                                    });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function (xhr) {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection