<?php

namespace App\Events;

use App\Models\CsvImport;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CsvImportProgressUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public CsvImport $csvImport
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('csv-import.' . $this->csvImport->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'progress.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->csvImport->id,
            'status' => $this->csvImport->status,
            'progress' => $this->csvImport->progress,
            'stats' => [
                'total_rows' => $this->csvImport->total_rows,
                'imported' => $this->csvImport->imported,
                'duplicates' => $this->csvImport->duplicates,
                'errors' => $this->csvImport->errors,
            ],
            'processed_chunks' => $this->csvImport->processed_chunks,
            'total_chunks' => $this->csvImport->total_chunks,
        ];
    }
}
