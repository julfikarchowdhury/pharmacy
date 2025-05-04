<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipRequest;
use App\Models\Tip;
use App\Traits\HandlesDeleteExceptions;
use App\Traits\ImageHelper;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TipController extends Controller
{
    use ImageHelper, HandlesDeleteExceptions;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Tip::query();

            return DataTables::of($query)
                ->addColumn('actions', function ($tip) {
                    return '
                <a href="' . route('tips.edit', $tip->id) . '" class="btn btn-info btn-sm">
                    <i class="fas fa-pen"></i>
                </a>
                <button class="btn btn-danger btn-sm" onclick="deleteTip(' . $tip->id . ')">
                    <i class="fas fa-trash"></i>
                </button>
                ';
                })
                ->addColumn('status', function ($tip) {
                    return $tip->status == 'active'
                        ? '<span class="badge badge-success" style="cursor: pointer;" onclick="changeStatus(' . $tip->id . ')">Active</span>'
                        : '<span class="badge badge-danger" style="cursor: pointer;" onclick="changeStatus(' . $tip->id . ')">Inactive</span>';
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('type') != "") {
                        $instance->where('type', $request->get('type'));
                    }
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        return view('admin.tips.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tips.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TipRequest $request)
    {
        DB::beginTransaction();

        try {
            $imagePath = $request->hasFile('image')
                ? $this->saveNewImage($request->file('image'), 'tips')
                : null;
            $videoPath = $request->hasFile('video')
                ? $this->saveNewImage($request->file('video'), 'tips_videos')
                : null;
            Tip::create([
                'type' => $request->type,
                'title_en' => $request->title_en,
                'title_bn' => $request->title_bn,
                'image' => $imagePath,
                'video' => $videoPath,
                'instruction_en' => $request->instruction_en,
                'instruction_bn' => $request->instruction_bn,
                'status' => $request->status
            ]);

            DB::commit();

            return redirect()->route('tips.index')->with(
                'success',
                'Tip created successfully'
            );
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error creating tip: ' . $e->getMessage());

            return redirect()->back()->with(
                'error',
                'Failed to create tip'
            )->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tip $tip)
    {
        return view('admin.tips.edit', compact('tip'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TipRequest $request, Tip $tip)
    {
        DB::beginTransaction();

        try {
            $imagePath = $tip->image;
            $videoPath = $tip->video;
            if ($request->hasFile('image')) {
                $this->deleteImage($tip->image);
                $imagePath = $this->saveNewImage($request->file('image'), 'tips');
            }
            if ($request->hasFile('video')) {
                $this->deleteImage($tip->video);
                $videoPath = $this->saveNewImage($request->file('video'), 'tips_videos');
            }

            $tip->update([
                'type' => $request->type,
                'title_en' => $request->title_en,
                'title_bn' => $request->title_bn,
                'image' => $imagePath,
                'video' => $videoPath,
                'instruction_en' => $request->instruction_en,
                'instruction_bn' => $request->instruction_bn,
                'status' => $request->status
            ]);

            DB::commit();

            return redirect()->route('tips.index')->with(
                'success',
                'Tip updated successfully'
            );
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error updating tip: ' . $e->getMessage());

            return redirect()->back()->with(
                'error',
                'Failed to update tip'
            )->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tip $tip)
    {
        return $this->handleDelete(
            function () use ($tip) {
                $this->deleteImage($tip->image);
                $this->deleteImage($tip->video);
                $tip->delete();
            },
            'Tip',
        );
    }

    /**
     * Change the status of the medicine.
     */
    public function changeStatus(Tip $tip): JsonResponse
    {
        try {
            $newStatus = $tip->status === 'active' ? 'inactive' : 'active';
            $tip->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        } catch (Exception $e) {
            Log::error('Error updating tip status: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to update tip status.'
                ],
                500
            );
        }
    }
}
