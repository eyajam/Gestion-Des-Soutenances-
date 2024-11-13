<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Defense extends Model
{
    use HasFactory;
    protected $table = 'defenses';

    // Allow mass assignment for the following fields
    protected $fillable = [
        'date',
        'time',
        'classroom',
        'cins',
        'students',
        'title',
        'emails',
        'specialty',
        'email_encadrant',
        'email_president',
        'email_rapporteur'  
    ];

    // Optionally, you can specify the date fields as Carbon instances for date formatting
    protected $dates = [
        'date',
        'time'
    ];

    // Define relationships if needed
    public function encadrant()
    {
        return $this->belongsTo(User::class, 'email_encadrant', 'email');
    }

}
