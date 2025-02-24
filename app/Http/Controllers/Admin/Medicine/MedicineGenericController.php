<?php

namespace App\Http\Controllers\Admin\Medicine;

use App\Http\Controllers\Controller;
use App\Http\Requests\MedicineGenericRequest;
use App\Models\MedicineGeneric;
use App\Traits\HandlesDeleteExceptions;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
/**
 * Controller for managing medicine generics in the admin panel.
 */
class MedicineGenericController extends Controller
{
    use HandlesDeleteExceptions;

    /**
     * Display a listing of the medicine generics.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = MedicineGeneric::query();

            return DataTables::of($query)
                ->addColumn('actions', function ($generic) {
                    return '
                    <button class="btn btn-info btn-sm" data-toggle="modal"
                        data-target="#editMedicineGenericModal" data-id="' . $generic->id . '"
                        data-title_bn="' . $generic->title_bn . '" data-title_en="' . $generic->title_en . '">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteMedicineGeneric(' . $generic->id . ')">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.medicine_attributes.medicine_generics');
    }

    /**
     * Store a newly created medicine generic in the database.
     */
    public function store(MedicineGenericRequest $request): RedirectResponse
    {
        try {
            MedicineGeneric::create([
                'title_en' => $request->title_en,
                'title_bn' => $request->title_bn,
            ]);

            return redirect()->back()->with(
                'success',
                'Medicine generic created successfully'
            );
        } catch (Exception $e) {
            Log::error('Error creating medicine generic: ' . $e->getMessage());

            return redirect()->back()->with(
                'error',
                'Failed to create medicine generic'
            );
        }
    }

    /**
     * Update the specified medicine generic in the database.
     */
    public function update(
        MedicineGenericRequest $request,
        MedicineGeneric $medicine_generic
    ): JsonResponse {
        try {
            $medicine_generic->update([
                'title_en' => $request->title_en,
                'title_bn' => $request->title_bn,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Medicine generic updated successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Error updating medicine generic: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update medicine generic'
            ]);
        }
    }

    /**
     * Remove the specified medicine generic from the database.
     */
    public function destroy(MedicineGeneric $medicine_generic): JsonResponse
    {
        return $this->handleDelete(
            function () use ($medicine_generic) {
                $medicine_generic->delete();
            },
            'Medicine Generic',
        );
    }

}
