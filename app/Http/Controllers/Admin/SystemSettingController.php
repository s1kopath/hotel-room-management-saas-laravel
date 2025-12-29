<?php

namespace App\Http\Controllers\Admin;

use App\Models\SystemSetting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SystemSettingController extends Controller
{
    /**
     * Display a listing of system settings
     */
    public function index()
    {
        // Only super admin can view system settings
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only super administrators can manage system settings.');
        }

        $settings = SystemSetting::orderBy('setting_key')->get();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update system settings
     */
    public function update(Request $request)
    {
        // Only super admin can update system settings
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only super administrators can manage system settings.');
        }

        $request->validate([
            'settings' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->settings as $key => $value) {
                $setting = SystemSetting::where('setting_key', $key)->first();
                if ($setting) {
                    $setting->update([
                        'setting_value' => $value,
                        'updated_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'System settings updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating settings: ' . $e->getMessage());
        }
    }

    /**
     * Create a new system setting
     */
    public function store(Request $request)
    {
        // Only super admin can create system settings
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only super administrators can manage system settings.');
        }

        $request->validate([
            'setting_key' => 'required|string|max:100|unique:system_settings,setting_key',
            'setting_value' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        try {
            SystemSetting::create([
                'setting_key' => $request->setting_key,
                'setting_value' => $request->setting_value,
                'description' => $request->description,
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'System setting created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating setting: ' . $e->getMessage());
        }
    }

    /**
     * Delete a system setting
     */
    public function destroy(string $id)
    {
        // Only super admin can delete system settings
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Only super administrators can manage system settings.');
        }

        $setting = SystemSetting::findOrFail($id);

        try {
            $setting->delete();
            return back()->with('success', 'System setting deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting setting: ' . $e->getMessage());
        }
    }
}

