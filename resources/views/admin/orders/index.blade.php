@extends('admin.layouts.master')

@section('content')
    <!-- breadcrumbs -->
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-2 text-gray-800">Orders </h1>
        <nav aria-label="breadcrumb" class="d-flex align-items-center">
            <ol class="breadcrumb mb-0 bg-transparent">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Orders </li>
            </ol>
        </nav>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Orders Table</h6>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <select id="orderType" class="form-control">
                        <option value="">All Order</option>
                        <option value="manual">Manaul Order</option>
                        <option value="direct">Direct Order</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="pharmacyFilter" class="form-control">
                        <option value="">All Pharmacies</option>
                        @foreach ($pharmacies as $pharmacy)
                            <option value="{{ $pharmacy->id }}">{{ $pharmacy->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="statusFilter" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="order_placed">Order Placed</option>
                        <option value="store_accepts">Store Accepts</option>
                        <option value="store_rejects">Store Rejects</option>
                        <option value="ready_for_rider">Ready for Rider</option>
                        <option value="rider_assigned">Rider Assigned</option>
                        <option value="out_for_delivery">Out for Delivery</option>
                        <option value="delivered">Delivered</option>
                        <option value="canceled">Canceled</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="ordersTable">
                    <thead>
                        <tr>
                            <th>Tracking Id</th>
                            <th>Order Type</th>
                            <th>Pharmacy Name</th>
                            <th>Customer Name</th>
                            <th>Total Amount</th>
                            <th>Payment Status</th>
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
            $(document).ready(function () {
                var table = $('#ordersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('orders.index') }}',
                        method: 'GET',
                        data: function (d) {
                            d.pharmacy_id = $('#pharmacyFilter').val();
                            d.status = $('#statusFilter').val();
                            d.order_type = $('#orderType').val();
                        }
                    },
                    columns: [
                        { data: 'tracking_id', name: 'tracking_id' },
                        {
                            data: 'order_type',
                            name: 'order_type',
                            render: function (data, type, row) {
                                return data.charAt(0).toUpperCase() + data.slice(1);
                            }
                        },
                        { data: 'pharmacy_name', name: 'pharmacy_name' },
                        { data: 'customer_name', name: 'customer_name' },
                        { data: 'total', name: 'total' },
                        { data: 'payment_status', name: 'payment_status' },
                        { data: 'status', name: 'status' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false }
                    ]
                });

                // Trigger filter change
                $('#pharmacyFilter, #statusFilter,#orderType').change(function () {
                    table.ajax.reload();
                });
            });
            $(document).on('change', '.order-status', function () {
                var selectedStatus = $(this).val();
                var orderId = $(this).data('order-id');
                var prevStatus = $(this).data('prev-status');

                // Define the allowed status transition order
                var allowedTransitions = {
                    'order_placed': ['store_accepts', 'store_rejects'],
                    'store_accepts': ['ready_for_rider', 'store_rejects'],
                    'store_rejects': ['store_accepts'],
                    'ready_for_rider': ['rider_assigned'],
                    'rider_assigned': ['out_for_delivery'],
                    'out_for_delivery': ['delivered'],
                    'delivered': [],
                    'canceled': []
                };

                if ((prevStatus === 'delivered' && selectedStatus === 'canceled') ||
                    (prevStatus === 'canceled' && selectedStatus === 'delivered')) {
                    Swal.fire('Invalid Action!', 'You cannot change a delivered or canceled order.', 'error');
                    $(this).val(prevStatus);
                    return;
                }
                if (selectedStatus === 'store_accepts' ||
                    selectedStatus === 'store_rejects' || selectedStatus === 'ready_for_rider') {
                    Swal.fire('Unauthorized Action!', 'Only store can add update this status.', 'error');
                    $(this).val(prevStatus);
                    return;
                }
                // Check if the selected status is allowed based on the current status
                if (!allowedTransitions[prevStatus].includes(selectedStatus) && selectedStatus !== 'canceled') {
                    Swal.fire('Invalid Transition!', 'You can\'t skip to this status. Please follow the correct order.', 'error');
                    $(this).val(prevStatus);
                    return;
                }

                // Show confirmation alert for valid status change
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
                        // Perform the AJAX request to update the status
                        $.ajax({
                            url: '/orders/change-status/' + orderId,
                            method: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                status: selectedStatus
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
                    } else {
                        // Reset the select value to the previous status if the update is canceled
                        $(this).val(prevStatus);
                    }
                });
            });

            // Save the previous status before the change (to reset if needed)
            $(document).on('focus', '.order-status', function () {
                $(this).data('prev-status', $(this).val());
            });
            function changePaymentStatus(orderId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to change the order payment's status?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/order/change-payment-status/' + orderId,
                            method: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire('Updated!', 'The payment status has been changed.', 'success');
                                    $('#order paymentsTable').DataTable().ajax.reload(); // Reload the DataTable
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