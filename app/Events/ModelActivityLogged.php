<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModelActivityLogged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $modelType;
    public $modelId;
    public $action;
    public $userId;
    public $changes;

    /**
     * Create a new event instance.
     */
    public function __construct($modelType, $modelId, $action, $userId = null, $changes = [])
    {
        $this->modelType = $modelType;
        $this->modelId = $modelId;
        $this->action = $action; // created, updated, deleted
        $this->userId = $userId;
        $this->changes = $changes;
    }
}
