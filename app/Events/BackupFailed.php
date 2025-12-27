<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BackupFailed
{
    use Dispatchable, SerializesModels;

    public string $type;
    public string $error;

    /**
     * Create a new event instance.
     */
    public function __construct(string $type, string $error)
    {
        $this->type = $type;
        $this->error = $error;
    }
}



