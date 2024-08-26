<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'project_title',
        'project_type',
        'partner',
        'company',
        'teacher_email',
        'specs'
    ];
public function student()
    {
    return $this->belongsTo(Student::class, 'student_id', 'user_id');
    }
public function teacher()
    {
    return $this->belongsTo(Teacher::class, 'teacher_email', 'email');
    }
}
