<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Log;

trait ImageHelper
{
    public function deleteImage($imagePath)
    {
        try {
            if ($imagePath && file_exists(public_path($imagePath))) {
                unlink(public_path($imagePath));
            }
        } catch (Exception $e) {
            Log::error('Error deleting image: ' . $e->getMessage());
        }
    }

    public function updateImage($image, $oldImagePath, $folder)
    {
        try {
            $this->deleteImage($oldImagePath);
            return $this->saveNewImage($image, $folder);
        } catch (Exception $e) {
            Log::error('Error updating image: ' . $e->getMessage());
            return null; // Or handle accordingly
        }
    }

    public function saveNewImage($image, $folder)
    {
        try {
            $publicPath = 'uploads/' . $folder;
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path($publicPath), $imageName);
            return $publicPath . '/' . $imageName;
        } catch (Exception $e) {
            Log::error('Error saving new image: ' . $e->getMessage());
            return null; // Or handle accordingly
        }
    }

}
