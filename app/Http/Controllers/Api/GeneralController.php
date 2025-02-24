<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PharmacyResource;
use App\Http\Resources\TipsResource;
use App\Models\Pharmacy;
use App\Models\Tip;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function gymTipTitles(Request $request, $type)
    {
        $languageColumn = $request->get('lang') === 'bn' ? 'title_bn' : 'title_en';

        $tips = Tip::where([
            'type' => $type,
            'status' => 'active'
        ])->pluck($languageColumn, 'id');

        return response()->json([
            'success' => true,
            'message' => 'Gym tip titles retrieved successfully.',
            'data' => $tips
        ]);
    }

    public function tipsDetails(Tip $tip)
    {
        return response()->json([
            'success' => true,
            'message' => 'Medicine details retrieved successfully.',
            'data' => new TipsResource($tip)
        ]);
    }

    public function allPharmacies(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'All pharmacies retrieved successfully.',
            'data' => PharmacyResource::collection(Pharmacy::get())
        ]);
    }
    
}
