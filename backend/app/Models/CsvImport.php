<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsvImport extends Model
{
    protected $fillable = [
        'file_path',
        'status',
        'total_chunks',
        'processed_chunks',
        'total_rows',
        'imported',
        'duplicates',
        'errors',
    ];

    protected $casts = [
        'total_chunks' => 'integer',
        'processed_chunks' => 'integer',
        'total_rows' => 'integer',
        'imported' => 'integer',
        'duplicates' => 'integer',
        'errors' => 'integer',
    ];

    /**
     * Get progress percentage
     */
    public function getProgressAttribute(): float
    {
        if ($this->total_chunks === 0) {
            return 0;
        }

        return round(($this->processed_chunks / $this->total_chunks) * 100, 2);
    }

    /**
     * Check if import is complete
     */
    public function isComplete(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if import failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }
}
