<?php

namespace Tourad\UserManager\Traits;

use Tourad\UserManager\Models\UserActivity;

trait LogsActivity
{
    /**
     * Boot the LogsActivity trait
     */
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            UserActivity::log(
                'created',
                class_basename($model) . ' created',
                $model,
                $model->getAttributes()
            );
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']); // Remove updated_at from changes
            
            if (!empty($changes)) {
                UserActivity::log(
                    'updated',
                    class_basename($model) . ' updated',
                    $model,
                    [
                        'changes' => $changes,
                        'original' => $model->getOriginal()
                    ]
                );
            }
        });

        static::deleted(function ($model) {
            UserActivity::log(
                'deleted',
                class_basename($model) . ' deleted',
                $model,
                $model->getAttributes()
            );
        });
    }

    /**
     * Log a custom activity
     */
    public function logActivity(string $type, string $description, array $data = [])
    {
        return UserActivity::log($type, $description, $this, $data);
    }
}