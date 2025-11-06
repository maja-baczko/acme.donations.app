<?php

namespace App\Modules\Administration\Http\Controllers;

use App\Modules\Administration\Http\Resources\SystemSettingResource;
use App\Modules\Administration\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingController {
    public function index() {
        return SystemSettingResource::collection(SystemSetting::all());
    }

    public function create(Request $request) {
        $data = $request->validate([
            'key' => ['required'],
            'value' => ['required'],
            'type' => ['required'],
            'description' => ['required'],
            'is_public' => ['boolean'],
        ]);

        return new SystemSettingResource(SystemSetting::create($data));
    }

    public function show(SystemSetting $systemSetting) {
        return new SystemSettingResource($systemSetting);
    }

    public function update(Request $request, SystemSetting $systemSetting) {
        $data = $request->validate([
            'key' => ['required'],
            'value' => ['required'],
            'type' => ['required'],
            'description' => ['required'],
            'is_public' => ['boolean'],
        ]);

        $systemSetting->update($data);

        return new SystemSettingResource($systemSetting);
    }

    public function destroy(SystemSetting $systemSetting) {
        $systemSetting->delete();

        return response()->json();
    }
}
