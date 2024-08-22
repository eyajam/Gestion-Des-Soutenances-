<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'number',
        'cin',
        'specialty',
        'status',
        'email',
        'password'
    ];

    protected $hidden = [
        'password',
    ];
}
