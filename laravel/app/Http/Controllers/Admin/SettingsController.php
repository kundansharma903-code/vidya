<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    private function instituteId(): int
    {
        return Auth::user()->institute_id;
    }

    public function index(Request $request)
    {
        $instituteId  = $this->instituteId();
        $institute    = DB::table('institutes')->where('id', $instituteId)->first();
        $settings     = DB::table('institute_settings')->where('institute_id', $instituteId)->first();
        $studentCount = DB::table('students')->where('institute_id', $instituteId)->where('is_active', true)->count();
        $tab          = in_array($request->tab, ['profile', 'notifications', 'ai']) ? $request->tab : 'profile';

        return view('admin.settings.index', compact('institute', 'settings', 'studentCount', 'tab'));
    }

    public function updateProfile(Request $request)
    {
        $instituteId = $this->instituteId();

        $data = $request->validate([
            'name'                 => 'required|string|max:150',
            'address'              => 'nullable|string|max:500',
            'phone'                => 'nullable|string|max:20',
            'email'                => 'nullable|email|max:150',
            'website'              => 'nullable|string|max:200',
            'timezone'             => 'nullable|string|max:50',
            'academic_year_format' => 'nullable|string|max:20',
        ]);

        DB::table('institutes')->where('id', $instituteId)->update([
            'name'       => $data['name'],
            'address'    => $data['address'],
            'phone'      => $data['phone'],
            'email'      => $data['email'],
            'website'    => $data['website'],
            'timezone'   => $data['timezone'] ?? 'Asia/Kolkata',
            'updated_at' => now(),
        ]);

        $this->ensureSettings($instituteId);
        DB::table('institute_settings')->where('institute_id', $instituteId)->update([
            'academic_year_format' => $data['academic_year_format'] ?? 'YYYY-YYYY',
            'updated_at'           => now(),
        ]);

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $path = $request->file('logo')->store('logos', 'public');
            DB::table('institutes')->where('id', $instituteId)->update(['logo_path' => $path]);
        }

        return redirect()->route('admin.settings', ['tab' => 'profile'])->with('success', 'Institute profile saved.');
    }

    public function updateNotifications(Request $request)
    {
        $instituteId = $this->instituteId();

        $data = $request->validate([
            'smtp_host'                  => 'nullable|string|max:200',
            'smtp_port'                  => 'nullable|integer|min:1|max:65535',
            'smtp_username'              => 'nullable|string|max:200',
            'smtp_password'              => 'nullable|string|max:200',
            'smtp_encryption'            => 'nullable|in:tls,ssl,none',
            'at_risk_threshold'          => 'nullable|numeric|min:0|max:100',
        ]);

        $data['notification_email_enabled'] = $request->boolean('notification_email_enabled');
        $data['weekly_digest_enabled']      = $request->boolean('weekly_digest_enabled');

        $this->ensureSettings($instituteId);
        DB::table('institute_settings')->where('institute_id', $instituteId)
            ->update(array_merge($data, ['updated_at' => now()]));

        return redirect()->route('admin.settings', ['tab' => 'notifications'])->with('success', 'Notification settings saved.');
    }

    public function updateAI(Request $request)
    {
        $instituteId = $this->instituteId();

        $data = $request->validate([
            'gemini_api_key'               => 'nullable|string|max:200',
            'ai_mode'                      => 'nullable|in:auto,gemini_only,template_only',
            'ai_circuit_breaker_threshold' => 'nullable|integer|min:1|max:100',
            'ai_cache_ttl'                 => 'nullable|integer|min:60|max:86400',
        ]);

        $this->ensureSettings($instituteId);
        DB::table('institute_settings')->where('institute_id', $instituteId)
            ->update(array_merge($data, ['updated_at' => now()]));

        return redirect()->route('admin.settings', ['tab' => 'ai'])->with('success', 'AI settings saved.');
    }

    private function ensureSettings(int $instituteId): void
    {
        DB::table('institute_settings')->insertOrIgnore([
            'institute_id' => $instituteId,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
}
