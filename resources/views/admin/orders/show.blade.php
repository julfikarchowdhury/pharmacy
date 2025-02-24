@extends('admin.layouts.master')

@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-2 text-gray-800">Order Details</h1>
        <nav aria-label="breadcrumb" class="d-flex align-items-center">
            <ol class="breadcrumb mb-0 bg-transparent">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('orders.index') }}" class="text-primary">Direct Orders</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Order Details</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Order #{{ $order->tracking_id }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Customer Details</h5>
                    <p>Name: {{ $order->customer->name }}</p>
                    <p>Phone: {{ $order->customer->phone }}</p>
                    <p>Address: {{ $order->customer->address }}</p>
                </div>
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Pharmacy Details</h5>
                    <p>Name: {{ $order->pharmacy->name }}</p>
                    <p>Phone: {{ $order->pharmacy->owner->phone }}</p>
                    <p>Address: {{ $order->pharmacy->address }}</p>
                </div>
            </div>
            @if($order->order_type == 'manual')
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Note:</h5>
                            </div>
                            <div class="card-body">
                                <p>{{ $order->note }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Preacription:</h5>
                            </div>
                            <div class="card-body">
                                <img src="{{asset($order->prescription)}}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <h5 class="mt-4 font-weight-bold">Order Items</h5>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach ($order->orderDetails as $detail)
                        @php $subtotal = $detail->qty * ($detail->discounted_price ?? $detail->price); @endphp
                        <tr>
                            <td>{{ $detail->medicine->name_en }}</td>
                            <td>{{ $detail->qty }}</td>
                            <td>${{ number_format($detail->discounted_price ?? $detail->price, 2) }}</td>
                            <td>${{ number_format($subtotal, 2) }}</td>
                        </tr>
                        @php $total += $subtotal; @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Subtotal:</th>
                        <th>${{ number_format($order->sub_total, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Discount (by points):</th>
                        <th>- ${{ number_format($order->discount_by_points, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Discount (by pharmacy):</th>
                        <th>- ${{ number_format($order->pharmacy_discount, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Tax:</th>
                        <th>${{ number_format($order->tax, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Delivary Charge:</th>
                        <th>${{ number_format($order->delivery_charge, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Total:</th>
                        <th>${{ number_format($order->total, 2) }}</th>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-4">
                <label for="orderStatus" class="font-weight-bold">Update Order Status</label>
                <select class="form-control order-status" data-order-id="{{ $order->id }}">
                    @foreach ($statuses as $status => $label)
                        <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
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
                // Reset the select value to the previous status if the transition is not allowed
                $(this).val(prevStatus);
                return; // Exit the function if status change is not allowed
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
                                $('#directOrdersTable').DataTable().ajax.reload();
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
    </script>
@endsection