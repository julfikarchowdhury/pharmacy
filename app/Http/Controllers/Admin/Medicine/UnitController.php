<?php

namespace App\Http\Controllers\Admin\Medicine;

use App\Http\Controllers\Controller;
use App\Http\Requests\UnitRequest;
use App\Models\Unit;
use App\Traits\HandlesDeleteExceptions;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
class UnitController extends Controller
{
    use HandlesDeleteExceptions;

    /**
     * Display a listing of the medicine companies.
     */
    public function index()
    {
        $units = Unit::all();
        return view(
            'admin.medicine_attributes.units',
            compact('units')
        );
    }

    /**
     * Store a newly created Unit in the database.
     */
    public function store(UnitRequest $request): RedirectResponse
    {
        try {
            Unit::create([
                'value' => $request->value,
            ]);

            return redirect()->back()->with(
                'success',
                'Unit created successfully'
            );
        } catch (Exception $e) {
            Log::error('Error creating Unit: ' . $e->getMessage());

            return redirect()->back()->with(
                'error',
                'Failed to create Unit'
            );
        }
    }

    /**
     * Update the specified Unit in the database.
     */
    public function update(
        UnitRequest $request,
        Unit $unit
    ): JsonResponse {
        try {

            $unit->update([
                'value' => $request->value,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Unit updated successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Error updating Unit: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update Unit'
            ]);
        }
    }

    /**
     * Remove the specified Unit from the database.
     */
    public function destroy(Unit $unit): JsonResponse
    {
        return $this->handleDelete(
            function () use ($unit) {
                $unit->delete();
            },
            'Unit',
        );
    }
}
