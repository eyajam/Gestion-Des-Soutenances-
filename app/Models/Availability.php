<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacher_id',
        'date',
        'start_time',
        'end_time',
        'status',
    ];
    public function teacher()
    {
    return $this->belongsTo(Teacher::class, 'teacher_id', 'user_id');
    }
}
