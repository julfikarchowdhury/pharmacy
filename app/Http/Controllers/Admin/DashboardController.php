<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingsRequest;
use App\Models\Setting;
use App\Traits\ImageHelper;
use Illuminate\Support\Facades\Log;
use Exception;
class DashboardController extends Controller
{
    use ImageHelper;

    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }
    public function settings()
    {
        return view('admin.settings');
    }
    public function updateSettings(SettingsRequest $request)
    {
        try {
            $validated = $request->validated();
            $settings = Setting::first();

            $settings->app_name = $validated['app_name'];
            $settings->currency_code = $validated['currency_code'];
            $settings->currency_icon = $validated['currency_icon'];
            $settings->points_conversion = $validated['points_conversion'] ?? $settings->points_conversion;
            $settings->delivery_charge_rate = $validated['delivery_charge_rate'] ?? $settings->delivery_charge_rate;
            $settings->tax_percentage = $validated['tax_percentage'] ?? $settings->tax_percentage;

            if ($request->hasFile('logo')) {
                $settings->logo = $this->updateImage(
                    $request->file('logo'),
                    $settings->logo,
                    'logos'
                );
            }

            $settings->save();

            return redirect()->route('settings')->with(
                'success',
                'Settings updated successfully.'
            );
        } catch (Exception $e) {
            Log::error('Error updating settings: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'An error occurred while updating settings. Please try again later.'
            ]);
        }
    }

}
