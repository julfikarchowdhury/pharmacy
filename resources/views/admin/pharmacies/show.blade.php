@extends('admin.layouts.master')

@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="h3 mb-2 text-gray-800">Pharmacy Details</h1>
        <nav aria-label="breadcrumb" class="d-flex align-items-center">
            <ol class="breadcrumb mb-0 bg-transparent">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('pharmacies.index') }}" class="text-primary">Pharmacies</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Pharmacy Details</li>
            </ol>
        </nav>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Pharmacy: {{ $pharmacy->name }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Owner Details</h5>
                    <p>Name: {{ $pharmacy->owner->name }}</p>
                    <p>Phone: {{ $pharmacy->owner->phone }}</p>
                    <p>Email: {{ $pharmacy->owner->email }}</p>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <!-- Pharmacy Logo & Banner -->
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Pharmacy Logo</h5>
                            <img src="{{ asset($pharmacy->logo) }}" alt="Pharmacy Logo" class="img-fluid mb-3"
                                style="max-width: 150px;">
                        </div>
                        <div class="col-md-6 text-center">
                            <h5 class="font-weight-bold">Pharmacy Banner</h5>
                            <img src="{{ asset($pharmacy->banner) }}" alt="Pharmacy Banner" class="img-fluid mb-3"
                                style="max-width: 100%;">
                        </div>
                    </div>
                    <h5 class="font-weight-bold">Pharmacy Details</h5>
                    <p>Name: {{ $pharmacy->name }}</p>
                    <p>Phone: {{ $pharmacy->phone }}</p>
                    <p>Address: {{ $pharmacy->address }}</p>
                </div>
            </div>
            <div class="d-none">
                <h5 class="font-weight-bold mt-4">Location</h5>
                <div id="map" style="height: 300px; width: 100%;"></div>
            </div>



            <h5 class="mt-4 font-weight-bold">Available Medicines</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Price</th>
                        <th>Discount Percentage</th>
                        <th>Discount Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pharmacy->medicines as $medicine)
                                    @php
                                        $discountPercentage = $medicine->pivot->discount_percentage;

                                        $discountedPrice = $medicine->unit_price - ($medicine->unit_price * $discountPercentage / 100);
                                    @endphp
                                    <tr>
                                        <td>{{ $medicine->name_en }}</td>
                                        <td>${{ number_format($medicine->unit_price, 2) }}</td>
                                        <td>{{ $discountPercentage }}%</td>
                                        <td>${{ number_format($discountedPrice, 2) }}</td>
                                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function initMap() {
            var pharmacyLocation = { lat: {{ $pharmacy->latitude }}, lng: {{ $pharmacy->longitude }} };

            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: pharmacyLocation
            });

            var marker = new google.maps.Marker({
                position: pharmacyLocation,
                map: map,
                title: "{{ $pharmacy->name }}"
            });
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap" async
        defer></script>
@endsection