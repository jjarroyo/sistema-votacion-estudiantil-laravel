<?php
namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel; // Usaremos PrivateChannel
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // IMPORTANTE
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentImportProgressUpdated implements ShouldBroadcast // <--- Implementar ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $progressPercentage;
    public int $processedRows;
    public int $totalRowsEstimate;
    public ?int $userId;

    public function __construct(int $progressPercentage, int $processedRows, int $totalRowsEstimate, ?int $userId)
    {
        $this->progressPercentage = $progressPercentage;
        $this->processedRows = $processedRows;
        $this->totalRowsEstimate = $totalRowsEstimate;
        $this->userId = $userId;
    }

    public function broadcastOn(): array
    {
        // Canal privado para el usuario que inició la importación
        return [new PrivateChannel('import-progress.' . $this->userId)];
    }

    // Opcional: Nombre del evento para el frontend (por defecto es el nombre de la clase)
    // public function broadcastAs(): string
    // {
    //     return 'student.import.progress';
    // }
}