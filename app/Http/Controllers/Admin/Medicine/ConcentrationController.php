<?php

namespace App\Http\Controllers\Admin\Medicine;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConcentrationRequest;
use App\Models\Concentration;
use App\Traits\HandlesDeleteExceptions;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

/**
 * Controller for managing concentrations in the admin panel.
 */
class ConcentrationController extends Controller
{
    use HandlesDeleteExceptions;

    /**
     * Display a listing of the concentrations.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Concentration::query();

            return DataTables::of($query)
                ->addColumn('actions', function ($concentration) {
                    return '
                    <button class="btn btn-info btn-sm" data-toggle="modal"
                        data-target="#editConcentrationModal" data-id="' . $concentration->id . '"
                        data-value="' . $concentration->value . '">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteConcentration(' . $concentration->id . ')">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.medicine_attributes.concentrations');
    }

    /**
     * Store a newly created concentration in the database.
     */
    public function store(ConcentrationRequest $request): RedirectResponse
    {
        try {
            Concentration::create([
                'value' => $request->value,
            ]);

            return redirect()->back()->with(
                'success',
                'Concentration created successfully'
            );
        } catch (Exception $e) {
            Log::error('Error creating concentration: ' . $e->getMessage());

            return redirect()->back()->with(
                'error',
                'Failed to create concentration'
            );
        }
    }

    /**
     * Update the specified concentration in the database.
     */
    public function update(
        ConcentrationRequest $request,
        Concentration $concentration
    ): JsonResponse {
        try {
            $concentration->update([
                'value' => $request->value,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Concentration updated successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Error updating concentration: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update concentration'
            ]);
        }
    }

    /**
     * Remove the specified concentration from the database.
     */
    public function destroy(Concentration $concentration): JsonResponse
    {
        return $this->handleDelete(
            function () use ($concentration) {
                $concentration->delete();
            },
            'Concentration',
        );
    }
}
