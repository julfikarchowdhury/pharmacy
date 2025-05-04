<?php

namespace App\Http\Controllers\Admin\Medicine;

use App\Http\Controllers\Controller;
use App\Http\Requests\MedicineCompanyRequest;
use App\Models\MedicineCompany;
use App\Traits\HandlesDeleteExceptions;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

/**
 * Controller for managing medicine companies in the admin panel.
 */
class MedicineCompanyController extends Controller
{
    use HandlesDeleteExceptions;

    /**
     * Display a listing of the medicine companies.
     */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = MedicineCompany::query();

            return DataTables::of($query)
                ->addColumn('actions', function ($company) {
                    return '
                    <button class="btn btn-info btn-sm" data-toggle="modal"
                        data-target="#editMedicineCompanyModal" data-id="' . $company->id . '"
                        data-name_bn="' . $company->name_bn . '" data-name_en="' . $company->name_en . '">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteMedicineCompany(' . $company->id . ')">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.medicine_attributes.medicine_companies');
    }

    /**
     * Store a newly created medicine company in the database.
     */
    public function store(MedicineCompanyRequest $request): RedirectResponse
    {
        try {
            MedicineCompany::create([
                'name_en' => $request->name_en,
                'name_bn' => $request->name_bn,
            ]);

            return redirect()->back()->with(
                'success',
                'Medicine company created successfully'
            );
        } catch (Exception $e) {
            Log::error('Error creating medicine company: ' . $e->getMessage());

            return redirect()->back()->with(
                'error',
                'Failed to create medicine company'
            );
        }
    }

    /**
     * Update the specified medicine company in the database.
     */
    public function update(
        MedicineCompanyRequest $request,
        MedicineCompany $medicine_company
    ): JsonResponse {
        try {

            $medicine_company->update([
                'name_en' => $request->name_en,
                'name_bn' => $request->name_bn,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Medicine company updated successfully'
            ]);
        } catch (Exception $e) {
            Log::error('Error updating medicine company: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update medicine company'
            ]);
        }
    }

    /**
     * Remove the specified medicine company from the database.
     */
    public function destroy(MedicineCompany $medicine_company): JsonResponse
    {
        return $this->handleDelete(
            function () use ($medicine_company) {
                $medicine_company->delete();
            },
            'Medicine Company',
        );
    }


}
