<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SliderRequest;
use App\Models\Slider;
use App\Traits\HandlesDeleteExceptions;
use App\Traits\ImageHelper;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SliderController extends Controller
{
    use ImageHelper, HandlesDeleteExceptions;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sliders = Slider::latest()->get();

        return view(
            'admin.sliders',
            compact('sliders')
        );

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SliderRequest $request)
    {
        DB::beginTransaction();

        try {
            $imagePath = $request->hasFile('image')
                ? $this->saveNewImage($request->file('image'), 'sliders')
                : null;

            Slider::create([
                'title' => $request->title,
                'image' => $imagePath,
                'status' => $request->status
            ]);

            DB::commit();

            return redirect()->route('sliders.index')->with(
                'success',
                'Slider created successfully'
            );
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error creating slider: ' . $e->getMessage());

            return redirect()->back()->with(
                'error',
                'Failed to create slider'
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SliderRequest $request, Slider $slider)
    {
        DB::beginTransaction();

        try {
            $imagePath = $request->hasFile('image') ?
                $this->updateImage(
                    $request->file('image'),
                    $slider->image,
                    'sliders'
                ) : $slider->image;

            $slider->update([
                'title' => $request->title,
                'image' => $imagePath,
                'status' => $request->status
            ]);

            DB::commit();

          
            return response()->json([
                'status' => 'success',
                'message' => 'Slider updated successfully'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error updating slider: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update slider'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slider $slider)
    {
        return $this->handleDelete(
            function () use ($slider) {
                $this->deleteImage($slider->image);
                $slider->delete();
            },
            'Slider',
        );
    }

    /**
     * Change the status of the slider.
     */
    public function changeStatus(Slider $slider): JsonResponse
    {
        try {
            $newStatus = $slider->status === 'active' ? 'inactive' : 'active';
            $slider->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        } catch (Exception $e) {
            Log::error('Error updating slider status: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to update slider status.'
                ],
                500
            );
        }
    }
}
