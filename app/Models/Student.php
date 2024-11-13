<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'number',
        'cin',
        'specialty',
        'status',
        'email',
        'password',
        'session'
    ];
    protected $hidden = [
        'password',
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function project()
    {
    return $this->hasOne(Project::class, 'student_id', 'user_id');
    }
    public function complaints()
    {
    return $this->hasMany(Complaint::class, 'user_id', 'user_id');
    }


}
