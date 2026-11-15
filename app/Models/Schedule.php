<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_subject_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
    ];

    public function setStartTimeAttribute($value): void
    {
        $this->attributes['start_time'] = (strlen($value) === 5) ? "{$value}:00" : $value;
    }

    public function setEndTimeAttribute($value): void
    {
        $this->attributes['end_time'] = (strlen($value) === 5) ? "{$value}:00" : $value;
    }

    public function classSubject(): BelongsTo
    {
        return $this->belongsTo(ClassSubject::class);
    }
}
