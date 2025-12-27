<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BackupCompleted
{
    use Dispatchable, SerializesModels;

    public string $type;
    public string $path;
    public int $size;
    public float $duration;

    /**
     * Create a new event instance.
     */
    public function __construct(string $type, string $path, int $size, float $duration)
    {
        $this->type = $type;
        $this->path = $path;
        $this->size = $size;
        $this->duration = $duration;
    }
}



