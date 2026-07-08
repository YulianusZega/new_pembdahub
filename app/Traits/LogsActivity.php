<?php

namespace App\Traits;

use App\Events\ModelActivityLogged;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Boot the trait and register model event listeners
     */
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            event(new ModelActivityLogged(
                get_class($model),
                $model->id,
                'created',
                Auth::id(),
                $model->getAttributes()
            ));
        });

        static::updated(function ($model) {
            event(new ModelActivityLogged(
                get_class($model),
                $model->id,
                'updated',
                Auth::id(),
                $model->getChanges()
            ));
        });

        static::deleted(function ($model) {
            event(new ModelActivityLogged(
                get_class($model),
                $model->id,
                'deleted',
                Auth::id(),
                $model->getAttributes()
            ));
        });
    }
}
