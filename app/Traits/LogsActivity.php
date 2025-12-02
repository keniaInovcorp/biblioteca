<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Boot the trait to listen for model events.
     *
     * @return void
     */
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            self::logChange($model, 'created');
        });

        static::updated(function ($model) {
            self::logChange($model, 'updated');
        });

        static::deleted(function ($model) {
            self::logChange($model, 'deleted');
        });
    }

    /**
     * Log the model change to the database.
     *
     * @param  mixed  $model
     * @param  string $action
     * @return void
     */
    protected static function logChange($model, $action)
    {
        ActivityLog::create([
            'user_id'    => Auth::id(),
            'module'     => class_basename($model),
            'subject_id' => $model->id,
            'action'     => $action,
            'changes'    => $action === 'updated' ? $model->getChanges() : null,
            'ip_address' => Request::ip(),
            'browser'    => Request::header('User-Agent'),
        ]);
    }
}
