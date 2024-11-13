<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnsupervisedStudentPeriod extends Model
{
    use HasFactory;
    protected $table = 'unsupervised_student_periods';

    // Define the fillable fields
    protected $fillable = ['start_date', 'end_date', 'status'];
}
