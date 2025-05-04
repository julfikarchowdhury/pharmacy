<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Traits\ImageHelper;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    use ImageHelper;
    public function profile()
    {
        return view('admin.auth.profile');
    }
    public function updateProfile(AdminProfileRequest $request)
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $user = auth()->user();

            // Update name
            $user->name = $validated['name'];

            if ($request->hasFile('image')) {
                $user->image = $this->updateImage(
                    $request->file('image'),
                    $user->image,
                    'profile_images'
                );
            }

            $user->save();
            DB::commit();

            return redirect()->route('admin.profile')->with(
                'success',
                'Profile updated successfully.'
            );
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error updating profile: ' . $e->getMessage());

            return back()->with(
                'error',
                'An error occurred while updating your profile. Please try again later.'
            );
        }
    }

    public function updatePassword(ChangePasswordRequest $request)
    {
        try {
            $user = auth()->user();

            if (Hash::check($request->current_password, $user->password)) {
                $user->password = bcrypt($request->password);
                $user->save();

                return redirect()->route('admin.profile')->with(
                    'success',
                    'Password changed successfully.'
                );
            } else {
                return back()->with(
                    'error',
                    'Current password is incorrect.'
                );
            }
        } catch (Exception $e) {
            Log::error('Error changing password: ' . $e->getMessage());

            return back()->with(
                'error',
                'An error occurred while updating your profile. Please try again later.'
            );
        }
    }
}
