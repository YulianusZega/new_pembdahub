<?php

namespace App\Listeners;

use App\Events\ModelActivityLogged;
use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogModelActivity implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ModelActivityLogged $event): void
    {
        ActivityLog::create([
            'user_id' => $event->userId,
            'model_type' => $event->modelType,
            'model_id' => $event->modelId,
            'action' => $event->action,
            'changes' => json_encode($event->changes),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
