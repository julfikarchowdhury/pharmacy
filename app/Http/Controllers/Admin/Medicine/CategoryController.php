<?php

namespace App\Http\Controllers\Admin\Medicine;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Traits\ImageHelper;
use App\Traits\HandlesDeleteExceptions;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

/**
 * Controller for managing categories in the admin panel.
 */
class CategoryController extends Controller
{
    use ImageHelper, HandlesDeleteExceptions;

    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::all();
        return view(
            'admin.medicine_attributes.categories',
            compact('categories')
        );
    }

    /**
     * Store a newly created category in the database.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            $iconPath = $request->hasFile('icon')
                ? $this->saveNewImage($request->file('icon'), 'categories')
                : null;

            Category::create([
                'name_bn' => $request->name_bn,
                'name_en' => $request->name_en,
                'icon' => $iconPath,
                'status' => $request->status,
            ]);

            return redirect()->back()->with(
                'success',
                'Category created successfully'
            );
        } catch (Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());

            return redirect()->back()->with(
                'error',
                'Failed to create category'
            );
        }
    }

    /**
     * Update the specified category in the database.
     */
    public function update(
        CategoryRequest $request,
        Category $category
    ): JsonResponse {
        try {
            $iconPath = $request->hasFile('icon')
                ? $this->updateImage(
                    $request->file('icon'),
                    $category->icon,
                    'categories'
                )
                : $category->icon;

            $category->update([
                'name_bn' => $request->name_bn,
                'name_en' => $request->name_en,
                'icon' => $iconPath,
                'status' => $request->status,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Category updated successfully'
            ]);
        } catch (Exception $e) {

            Log::error('Error updating category: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update category'
            ]);
        }
    }

    /**
     * Remove the specified category from the database.
     */
    public function destroy(Category $category): JsonResponse
    {
        return $this->handleDelete(
            function () use ($category) {
                $this->deleteImage($category->icon);
                $category->delete();
            },
            'Category',
        );
    }

    public function changeStatus(Category $category): JsonResponse
    {
        try {
            $newStatus = $category->status === 'active' ? 'inactive' : 'active';
            $category->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        } catch (Exception $e) {
            Log::error('Error updating category status: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to update category status.'
                ],
                500
            );
        }
    }
}



