<?php
namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentImportProcessingCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $results;
    public ?int $userId;
    public bool $hasError; 

    public function __construct(array $results, ?int $userId, bool $hasError = false)
    {
        $this->results = $results;
        $this->userId = $userId;
        $this->hasError = $hasError;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('import-progress.' . $this->userId)];
    }
}