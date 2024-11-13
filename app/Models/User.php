<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Crypt;
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
    public function isPasswordEncrypted()
{
    // Vérifie si la chaîne commence par "$2y$" qui est le préfixe des hachages bcrypt
    if (strpos($this->password, '$2y$') === 0) {
        return false; // Le mot de passe est haché avec bcrypt
    }

    // Sinon, essayer de décrypter
    try {
        Crypt::decryptString($this->password);
        return true; // Le mot de passe est chiffré
    } catch (\Exception $e) {
        return false; // Le mot de passe n'est pas décryptable
    }
}

}
