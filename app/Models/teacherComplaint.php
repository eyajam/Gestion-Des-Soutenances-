<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class teacherComplaint extends Model
{
    use HasFactory;
    protected $table = 'teacher_complaints';
    protected $fillable = [
        'user_id',
        'teacher_email',
        'student_email',
        'object',
        'message',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function teacherComplaint()
    {
    return $this->belongsTo(Teacher::class, 'user_id', 'user_id');
    }
    public function student()
    {
    return $this->belongsTo(Student::class, 'student_email', 'email');
    }
    public function teacher()
    {
    return $this->belongsTo(Teacher::class, 'teacher_email', 'email');
    }
}
