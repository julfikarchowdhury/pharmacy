<?php

namespace App\Http\Controllers\Admin\Medicine;

use App\Http\Controllers\Controller;
use App\Http\Requests\MedicineRequest;
use App\Models\Medicine;
use App\Models\Category;
use App\Models\Concentration;
use App\Models\MedicineCompany;
use App\Models\MedicineGeneric;
use App\Traits\ImageHelper;
use App\Traits\HandlesDeleteExceptions;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller for managing medicines in the admin panel.
 */
class MedicineController extends Controller
{
    use ImageHelper, HandlesDeleteExceptions;

    /**
     * Display a listing of the medicines in DataTable.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $medicines = Medicine::with(['category', 'company', 'generic', 'images']);
            return DataTables::of($medicines)
                ->addColumn('name', function ($medicine) {
                    return $medicine->name_en . "-" . $medicine->concentration->value;
                })->addColumn('category', function ($medicine) {
                    return $medicine->category->name_en;
                })->addColumn('company', function ($medicine) {
                    return $medicine->company->name_en;
                })->addColumn('generic', function ($medicine) {
                    return $medicine->generic->title_en;
                })
                ->addColumn('image', function ($medicine) {
                    $image = $medicine->images->first();
                    if ($image) {
                        return '<img src="' . asset($image->src) . '" alt="Medicine Image" class="img-thumbnail" height="50"height="100">';
                    }
                    return '<img src="' . asset('default_image.jpg') . '" alt="Default Image" class="img-thumbnail" height="50"height="100">';
                })
                ->addColumn('actions', function ($medicine) {
                    return '
                <a href="' . route('medicines.edit', $medicine->id) . '" class="btn btn-info btn-sm">
                    <i class="fas fa-pen"></i>
                </a>
                    <button class="btn btn-danger btn-sm" onclick="deleteMedicine(' . $medicine->id . ')">
                        <i class="fas fa-trash"></i>
                    </button>
                    ';
                })
                ->addColumn('status', function ($medicine) {
                    return $medicine->status == 'active'
                        ? '<span class="badge badge-success" style="cursor: pointer;" onclick="changeStatus(' . $medicine->id . ')">Active</span>'
                        : '<span class="badge badge-danger" style="cursor: pointer;" onclick="changeStatus(' . $medicine->id . ')">Inactive</span>';
                })
                ->rawColumns(['actions', 'status', 'image'])
                ->make(true);
        }
        return view('admin.medicines.index');

    }



    /**
     * Show the form for creating a new medicine.
     */
    public function create()
    {
        $data['categories'] = Category::where('status', 'active')->select('id', 'name_en')->get();
        $data['companies'] = MedicineCompany::select('id', 'name_en')->get();
        $data['generics'] = MedicineGeneric::select('id', 'title_en')->get();
        $data['concentrations'] = Concentration::select('id', 'value')->get();

        return view(
            'admin.medicines.create',
            $data
        );
    }

    /**
     * Store a newly created medicine in the database.
     */
    public function store(MedicineRequest $request): RedirectResponse
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

            return redirect()->route('medicines.index')->with(
                'success',
                'Medicine created successfully'
            );
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error creating medicine: ' . $e->getMessage());

            return redirect()->back()->with(
                'error',
                'Failed to create medicine'
            )->withInput();
        }
    }

    public function show(Medicine $medicine)
    {
        $data['medicine'] = $medicine;
        $data['medicineImages'] = $medicine->images;

        if ($medicine->user_id) {
            $data['requested_by'] = $medicine->user->name; // Assuming user relationship exists
        } else {
            $data['requested_by'] = null;
        }

        return view('admin.medicines.show', $data);
    }

    /**
     * Show the form for editing the specified medicine.
     */

    public function edit(Medicine $medicine)
    {
        $data['categories'] = Category::where('status', 'active')->select('id', 'name_en')->get();
        $data['companies'] = MedicineCompany::select('id', 'name_en')->get();
        $data['generics'] = MedicineGeneric::select('id', 'title_en')->get();
        $data['concentrations'] = Concentration::select('id', 'value')->get();
        $data['medicine'] = $medicine;
        $data['medicineImages'] = $medicine->images;

        return view('admin.medicines.edit', $data);
    }

    /**
     * Update the specified medicine in the database.
     */
    public function update(MedicineRequest $request, Medicine $medicine): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $medicine->update(array_merge($request->validated(), ['user_id' => auth()->id()]));

            if ($request->has('units')) {
                $medicine->units()->sync($request->units);
            }
            $removedImages = json_decode($request->removed_images, true);
            if (!empty($removedImages)) {
                foreach ($removedImages as $imageId) {
                    $image = $medicine->images()->find($imageId);
                    if ($image) {
                        $this->deleteImage($image->src);
                        $image->delete();
                    }
                }
            }
            if ($request->hasFile('images')) {
                foreach ($medicine->images as $image) {
                    $this->deleteImage($image->src);
                    $image->delete();
                }

                foreach ($request->file('images') as $image) {
                    $imagePath = $this->saveNewImage($image, 'medicines');
                    $medicine->images()->create(['src' => $imagePath]);
                }
            }

            DB::commit();

            return redirect()->route('medicines.index')->with(
                'success',
                'Medicine updated successfully'
            );
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error updating medicine: ' . $e->getMessage());

            return redirect()->back()->with(
                'error',
                'Failed to update medicine'
            );
        }
    }

    /**
     * Remove the specified medicine from the database.
     */
    public function destroy(Medicine $medicine): JsonResponse
    {

        return $this->handleDelete(
            function () use ($medicine) {
                foreach ($medicine->images as $image) {
                    $this->deleteImage($image->src);
                    $image->delete();
                }
                $medicine->units()->detach();

                $medicine->delete();
            },
            'Medicine',
        );

    }


    /**
     * Change the status of the medicine.
     */
    public function changeStatus(Medicine $medicine): JsonResponse
    {
        try {
            $newStatus = $medicine->status === 'active' ? 'inactive' : 'active';
            $medicine->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        } catch (Exception $e) {
            Log::error('Error updating medicine status: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to update medicine status.'
                ],
                500
            );
        }
    }

    public function requestedMedicines(Request $request)
    {
        if ($request->ajax()) {
            $medicines = Medicine::where('status', 'inactive')
                ->whereHas('user', function ($query) {
                    $query->where('id', '!=', auth()->user()->id);
                })
                ->with(['user:id,name', 'category', 'company', 'generic', 'images'])->get();
            return DataTables::of($medicines)
                ->addColumn('name', function ($medicine) {
                    return $medicine->name_en . "-" . $medicine->concentration->value;
                })->addColumn('category', function ($medicine) {
                    return $medicine->category->name_en;
                })->addColumn('company', function ($medicine) {
                    return $medicine->company->name_en;
                })->addColumn('generic', function ($medicine) {
                    return $medicine->generic->title_en;
                })
                ->addColumn('image', function ($medicine) {
                    $image = $medicine->images->first();
                    if ($image) {
                        return '<img src="' . asset($image->src) . '" alt="Medicine Image" class="img-thumbnail" height="50"height="100">';
                    }
                    return '<img src="' . asset('default_image.jpg') . '" alt="Default Image" class="img-thumbnail" height="50"height="100">';
                })
                ->addColumn('actions', function ($medicine) {
                    return '
                <a href="' . route('medicines.show', $medicine->id) . '" class="btn btn-info btn-sm">
                    <i class="fas fa-eye"></i>
                </a>
                    <button class="btn btn-danger btn-sm" onclick="deleteMedicine(' . $medicine->id . ')">
                        <i class="fas fa-trash"></i>
                    </button>
                    ';
                })
                ->addColumn('status', function ($medicine) {
                    return $medicine->status == 'inactive'
                        ? '<span class="badge badge-success" style="cursor: pointer;" onclick="changeStatus(' . $medicine->id . ')">Accept</span>'
                        : '';
                })
                ->rawColumns(['actions', 'status', 'image'])
                ->make(true);
        }
        return view('admin.medicines.requested_medicines');

    }
}
