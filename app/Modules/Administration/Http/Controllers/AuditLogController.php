<?php

namespace App\Modules\Administration\Http\Controllers;

use App\Modules\Administration\Http\Resources\AuditLogResource;
use App\Modules\Administration\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController {
    public function index() {
        return AuditLogResource::collection(AuditLog::all());
    }

    public function create(Request $request) {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users'],
            'action' => ['required'],
            'entity_type' => ['required'],
            'entity_id' => ['required', 'integer'],
            'old_value' => ['required'],
            'new_value' => ['required'],
            'ip_address' => ['required'],
            'user_agent' => ['required'],
        ]);

        return new AuditLogResource(AuditLog::create($data));
    }

    public function show(AuditLog $auditLog) {
        return new AuditLogResource($auditLog);
    }

    public function update(Request $request, AuditLog $auditLog) {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users'],
            'action' => ['required'],
            'entity_type' => ['required'],
            'entity_id' => ['required', 'integer'],
            'old_value' => ['required'],
            'new_value' => ['required'],
            'ip_address' => ['required'],
            'user_agent' => ['required'],
        ]);

        $auditLog->update($data);

        return new AuditLogResource($auditLog);
    }

    public function destroy(AuditLog $auditLog) {
        $auditLog->delete();

        return response()->json();
    }
}
