<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacher_id',
        'first_name',
        'last_name',
        'grade',
        'email',
        'password'
    ];

    protected $hidden = [
        'password',
    ];

}
