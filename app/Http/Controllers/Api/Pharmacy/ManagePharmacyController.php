<?php

namespace App\Http\Controllers\Api\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddMedicineToPharmacyRequest;
use App\Http\Resources\MedicineResource;
use App\Models\Medicine;
use App\Models\Pharmacy;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ManagePharmacyController extends Controller
{

    public function addMedicines(AddMedicineToPharmacyRequest $request)
    {
        try {
            $pharmacyOwner = Auth::user();
            $pharmacy = $pharmacyOwner->pharmacy;

            if (!$pharmacy) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pharmacy not found.'
                ], 404);
            }

            $medicines = $request->input('medicines');

            $medicineIds = array_keys($medicines);
            $existingMedicines = Medicine::whereIn('id', $medicineIds)->pluck('id')->toArray();

            $invalidMedicineIds = array_diff($medicineIds, $existingMedicines);

            if (!empty($invalidMedicineIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more selected medicines do not exist in the database.',
                    'invalid_medicines' => $invalidMedicineIds
                ], 400);
            }

            $pivotData = [];
            foreach ($medicines as $medicineId => $discount) {
                $pivotData[$medicineId] = ['discount_percentage' => $discount];
            }

            $pharmacy->medicines()->sync($pivotData);

            return response()->json([
                'success' => true,
                'message' => 'Medicines added to the pharmacy successfully.'
            ], 200);

        } catch (Exception $e) {
            Log::error('Error adding medicines to pharmacy: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }
    public function getMedicinesNotSynced(Request $request, Pharmacy $pharmacy)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        // Get the medicines that are not already synced and have an 'active' status
        $medicinesNotSynced = Medicine::whereDoesntHave('pharmacies', function ($query) use ($pharmacy) {
            $query->where('pharmacy_id', $pharmacy->id)
            ;
        })->where('status', 'active')->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'LIKE', "%{$search}%")
                    ->orWhere('name_bn', 'LIKE', "%{$search}%")
                    ->orWhereHas('generic', function ($q) use ($search) {
                        $q->where('title_en', 'LIKE', "%{$search}%")
                            ->orWhere('title_bn', 'LIKE', "%{$search}%");
                    });
            });
        })->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Medicines retrieved successfully',
            'data' => MedicineResource::collection($medicinesNotSynced),
            'pagination' => $this->paginateData($medicinesNotSynced)
        ]);
    }
}
