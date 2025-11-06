<?php

namespace App\Modules\Administration\Services;

use App\Modules\Administration\Models\AuditLog;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Throwable;

class AuditLogService {
    /**
     * Create a new audit log
     *
     * @param array $data
     * @return AuditLog
     * @throws Throwable
     */
    public function create(array $data): AuditLog {
        return DB::transaction(function () use ($data) {
            // Add IP and user agent if not provided
            if (!isset($data['ip_address'])) {
                $data['ip_address'] = Request::ip();
            }
            if (!isset($data['user_agent'])) {
                $data['user_agent'] = Request::userAgent();
            }

            $auditLog = AuditLog::create($data);
            return $auditLog->fresh();
        });
    }

    /**
     * Log an action with convenience method
     *
     * @param User $user
     * @param string $action
     * @param string $entityType
     * @param int $entityId
     * @param array|null $oldValue
     * @param array|null $newValue
     * @return AuditLog
     * @throws Throwable
     */
    public function logAction(
        User $user,
        string $action,
        string $entityType,
        int $entityId,
        array $oldValue = null,
        array $newValue = null
    ): AuditLog {
        return $this->create([
            'user_id' => $user->id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Get all logs for a specific entity
     *
     * @param string $entityType
     * @param int $entityId
     * @return Collection
     */
    public function getLogsForEntity(string $entityType, int $entityId): Collection {
        return AuditLog::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all logs by a specific user
     *
     * @param int $userId
     * @return Collection
     */
    public function getLogsByUser(int $userId): Collection {
        return AuditLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent logs with limit
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentLogs(int $limit = 50): Collection {
        return AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Filter logs by various criteria
     *
     * @param array $filters
     * @return Collection
     */
    public function filterLogs(array $filters): Collection {
        $query = AuditLog::query()->with('user');

        // Filter by action
        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        // Filter by entity_type
        if (isset($filters['entity_type'])) {
            $query->where('entity_type', $filters['entity_type']);
        }

        // Filter by user_id
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter by date range
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Filter by IP address
        if (isset($filters['ip_address'])) {
            $query->where('ip_address', $filters['ip_address']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
