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
            return true; 
        }

        $oneWeekAgo = now()->subWeek();//une semaine avant la date d'aujourd'hui 
        /* 17(now)-7=10 le 10
        Le 15 j ai remis
        Rec(le15)>owa(le 10)
        Now : le 23 : 23-7 = 16
        Le 16> le 15 okk donc */
        return $lastComplaint->created_at < $oneWeekAgo;
    }
}
