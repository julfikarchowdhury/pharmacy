<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Traits\HandlesDeleteExceptions;
use App\Traits\ImageHelper;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
class PharmacyController extends Controller
{
    use ImageHelper,HandlesDeleteExceptions;
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $pharmacy = Pharmacy::with('owner')->get();

            return DataTables::of($pharmacy)
                ->addColumn('logo', function ($pharmacy) {
                    return '<img src="' . asset($pharmacy->logo) . '" alt="pharmacy Image" class="img-thumbnail" height="50"height="100">';
                })
                ->addColumn('actions', function ($pharmacy) {
                    return '
                <a href="' . route('pharmacies.show', $pharmacy->id) . '" class="btn btn-info btn-sm">
                    <i class="fas fa-info"></i>
                </a>
                <button class="btn btn-danger btn-sm" onclick="deletePharmacy(' . $pharmacy->id . ')">
                    <i class="fas fa-trash"></i>
                </button>
                ';
                })
                ->addColumn('status', function ($pharmacy) {
                    return $pharmacy->status == 'active'
                        ? '<span class="badge badge-success" style="cursor: pointer;" onclick="changeStatus(' . $pharmacy->id . ')">Active</span>'
                        : '<span class="badge badge-danger" style="cursor: pointer;" onclick="changeStatus(' . $pharmacy->id . ')">Inactive</span>';
                })
                ->rawColumns(['logo', 'actions', 'status'])
                ->make(true);
        }

        return view('admin.pharmacies.index');
    }
    public function show(Pharmacy $pharmacy)
    {
        $pharmacy->load(['owner', 'medicines']);
        return view('admin.pharmacies.show', compact('pharmacy'));

    }

    /**
     * Remove the specified pharmacy from the database.
     */
    public function destroy(Pharmacy $pharmacy): JsonResponse
    {

        return $this->handleDelete(
            function () use ($pharmacy) {
                $this->deleteImage($pharmacy->banner);
                $this->deleteImage($pharmacy->logo);
                $pharmacy->delete();
                $pharmacy->owner->delete();

            },
            'Pharmacy',
        );

    }


    /**
     * Change the status of the pharmacy.
     */
    public function changeStatus(Pharmacy $pharmacy): JsonResponse
    {
        try {
            $newStatus = $pharmacy->status === 'active' ? 'inactive' : 'active';
            $pharmacy->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        } catch (Exception $e) {
            Log::error('Error updating pharmacy status: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to update pharmacy status.'
                ],
                500
            );
        }
    }
}
