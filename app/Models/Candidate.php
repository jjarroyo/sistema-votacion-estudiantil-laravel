<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    protected $table = 'candidates';

    protected $fillable = [
        'student_id',
        'election_id',
        'proposal',
        'photo_path',
        'list_number',
    ];

    /**
     * Obtiene el estudiante asociado a esta candidatura.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Obtiene la elección a la que pertenece esta candidatura.
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function receivedVotes(): HasMany
    {
        return $this->hasMany(RecordedVote::class);
    }
}