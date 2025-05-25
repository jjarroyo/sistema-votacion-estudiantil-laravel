<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordedVote extends Model
{
    use HasFactory;

    protected $table = 'recorded_votes';

    protected $fillable = [
        'election_id',
        'candidate_id',
        'is_blank_vote',
    ];

    protected $casts = [
        'is_blank_vote' => 'boolean',
    ];

    /**
     * La elección a la que pertenece este voto.
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * El candidato por el que se votó (si no fue en blanco).
     */
    public function candidate(): BelongsTo
    {
        // candidate_id puede ser null, así que esta relación puede devolver null
        return $this->belongsTo(Candidate::class);
    }
}