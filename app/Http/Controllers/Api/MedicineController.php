<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MedicineRequest;
use App\Http\Resources\MedicineDetailsResource;
use App\Http\Resources\MedicineResource;
use App\Http\Resources\PharmacyResource;
use App\Models\Category;
use App\Models\Concentration;
use App\Models\Medicine;
use App\Models\MedicineCompany;
use App\Models\MedicineGeneric;
use App\Models\Pharmacy;
use App\Models\Unit;
use App\Traits\ImageHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MedicineController extends Controller
{
    use ImageHelper;

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
            'medicines' => MedicineResource::collection($medicines->items()),
            'pagination' => $this->paginateData($medicines)
        ]);
    }


    public function medicineWisePharmacies(
        Request $request,
        Medicine $medicine
    ) {
        $perPage = $request->input('per_page', 10);

        $pharmacies = $medicine->pharmacies()
            ->where('pharmacies.status', 'active')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Pharmacies retrieved successfully',
            'pharmacies' => PharmacyResource::collection($pharmacies->items()),
            'pagination' => $this->paginateData($pharmacies)
        ]);
    }


    public function medicineDetails(Medicine $medicine, Pharmacy $pharmacy)
    {
        if (!$pharmacy || !$medicine || !$pharmacy->medicines()->where('medicine_id', $medicine->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found in this pharmacy.'
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
            'pharmacies' => function ($query) use ($pharmacy) {
                $query->where('pharmacy_id', $pharmacy->id)
                    ->withPivot('discount_percentage'); // Load the discount_percentage field from the pivot table
            }
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
        })->filter()->unique('concentration')->values();

        return response()->json([
            'success' => true,
            'message' => 'Medicine details retrieved successfully.',
            'medicine_details' => [
                'medicine' => new MedicineDetailsResource($medicineDetails),
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

    public function addDrugDropdownData()
    {
        return response()->json([
            'success' => true,
            'message' => 'Pharmacies retrieved successfully',
            'concentrations' => Concentration::get(['id', 'value'])->map(fn($item) => ['id' => $item->id, 'value' => $item->value]),
            'medicine_companies' => MedicineCompany::get(['id', 'name_en'])->map(fn($item) => ['id' => $item->id, 'value' => $item->name_en]),
            'generics' => MedicineGeneric::get(['id', 'title_en'])->map(fn($item) => ['id' => $item->id, 'value' => $item->title_en]),
            'units' => Unit::get(['id', 'value_en'])->map(fn($item) => ['id' => $item->id, 'value' => $item->value_en]),
            'categories' => Category::where('status', 'active')->get(['id', 'name_en'])->map(fn($item) => ['id' => $item->id, 'value' => $item->name_en]),
        ]);

    }
}
