<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MedicineRequest;
use App\Http\Resources\MedicineDetailsResource;
use App\Http\Resources\MedicineResource;
use App\Http\Resources\PharmacyResource;
use App\Models\Medicine;
use App\Models\Pharmacy;
use App\Traits\ImageHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MedicineController extends Controller
{    use ImageHelper;

    public function allMedicines(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $medicines = Medicine::when($search, function ($query) use ($search) {
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
            'data' => MedicineResource::collection($medicines->items()),
            'pagination' => $this->paginateData($medicines)
        ]);
    }


    public function medicineWisePharmacies(
        Request $request,
        Medicine $medicine
    ) {
        $perPage = $request->input('per_page', 10);

        $pharmacies = $medicine->pharmacies()
            ->where('status', 'active')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Pharmacies retrieved successfully',
            'data' => PharmacyResource::collection($pharmacies->items()),
            'pagination' => $this->paginateData($pharmacies)
        ]);
    }


    public function medicineDetails(Medicine $medicine, Pharmacy $pharmacy)
    {
        if (!$pharmacy) {
            return response()->json([
                'success' => false,
                'message' => 'Pharmacy not found.'
            ], 404);
        }
        if (!$medicine) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found.'
            ], 404);
        }
        // Fetch medicine details with all relationships
        $medicineDetails = $medicine->load([
            'category',
            'company',
            'generic',
            'images',
            'units',
            'concentration',
        ]);

        $relatedMedicines = Medicine::where('medicine_company_id', $medicine->medicine_company_id)
            ->where('medicine_generic_id', $medicine->medicine_generic_id)
            ->where('concentration_id', '!=', $medicine->concentration_id)
            ->with('concentration')
            ->get();

        $relatedConcentrations = $relatedMedicines->map(function ($med) {
            return $med->concentration ? [
                'medicine_id' => $med->id,
                'concentration' => $med->concentration->value,
            ] : null;
        })->filter()->values();

        return response()->json([
            'success' => true,
            'message' => 'Medicine details retrieved successfully.',
            'data' => [
                'medicine_details' => new MedicineDetailsResource($medicineDetails),
                'related_concentrations' => $relatedConcentrations,
            ]
        ]);
    }

    public function addDrugRequest(MedicineRequest $request)
    {
        DB::beginTransaction();

        try {
            // Create the medicine entry
            $medicine = Medicine::create(array_merge($request->validated(), ['user_id' => auth()->id()]));

            if ($request->has('units')) {
                $medicine->units()->sync($request->units);
            }
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $this->saveNewImage($image, 'medicines');

                    // Store image in a pivot table or related model as necessary
                    $medicine->images()->create([
                        'src' => $imagePath,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Add Drug Request Successful!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error add drug request: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Add Drug Request Unuccessful!',
            ]);

        }
    }

}
