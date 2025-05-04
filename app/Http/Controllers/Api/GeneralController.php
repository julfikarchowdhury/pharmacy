<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicineResource;
use App\Http\Resources\PharmacyResource;
use App\Http\Resources\SliderResource;
use App\Http\Resources\TipsResource;
use App\Models\Pharmacy;
use App\Models\Slider;
use App\Models\Tip;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function gymTipTitles(Request $request, string $type)
    {  
        $languageColumn = $request->get('lang') === 'bn' ? 'title_bn' : 'title_en';

        $tips = Tip::where([
            'type' => $type,
            'status' => 'active',
        ])->get(['id', $languageColumn])->map(function ($tip) use ($languageColumn) {
            return [
                'id' => $tip->id,
                'title' => $tip->$languageColumn,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => ucfirst($type) . ' tip titles retrieved successfully.',
            'tips' => $tips,
        ]);
    }
    public function tipsDetails(Tip $tip)
    {
        return response()->json([
            'success' => true,
            'message' => 'Tip details retrieved successfully.',
            'data' => new TipsResource($tip)
        ]);
    }

    public function allPharmacies(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'All pharmacies retrieved successfully.',
            'pharmacies' => PharmacyResource::collection(Pharmacy::get())
        ]);
    }
    public function sliders()
    {
        return response()->json([
            'success' => true,
            'message' => 'All sliders retrieved successfully.',
            'sliders' => SliderResource::collection(Slider::where('status', 'active')->get())
        ]);

    }

    public function getMedicinesByPharmacy(Pharmacy $pharmacy)
    {
        $medicines = $pharmacy->medicines()->with('units')->get();

        return response()->json([
            'success' => true,
            'message' => 'Medicines retrieved successfully',
            'medicines' => MedicineResource::collection($medicines),
        ]);
    }

}
