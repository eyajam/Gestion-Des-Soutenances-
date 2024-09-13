<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'complaint_type',
        'object',
        'message',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // App\Models\Complaint.php
    public function student()
    {
    return $this->belongsTo(Student::class, 'user_id', 'user_id');
    }

    public static function canSubmitComplaint($userId)
    {
        $lastComplaint = self::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastComplaint) {
            return true; // No complaints of this type submitted yet
        }

        $oneWeekAgo = now()->subWeek();//une semaine avant la date d'aujourd'hui
        return $lastComplaint->created_at < $oneWeekAgo;
    }
}
