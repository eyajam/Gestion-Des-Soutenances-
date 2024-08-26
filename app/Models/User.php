<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastName',
        'email',
        'role',
        'password',
        'verification_code',
        'is_verified'
    ];
   
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id'); // Assuming 'user_id' is the foreign key in the students table
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id'); // Assuming 'user_id' is the foreign key in the teachers table
    }
    
}
