<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'grade',
        'email',
        'password',
        'remaining_availabilities',
        'is_president',
        'is_rapporteur',
        'is_successive'
    ];
    
    protected $hidden = [
        'password',
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function availabilities()
    {
        return $this->hasMany(Availability::class, 'teacher_id','user_id');
    }
}
