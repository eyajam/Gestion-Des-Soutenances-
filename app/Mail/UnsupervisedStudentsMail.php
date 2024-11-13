<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UnsupervisedStudentsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $unsupervisedStudents;

    public function __construct($unsupervisedStudents)
    {
        $this->unsupervisedStudents = $unsupervisedStudents;
    }

    public function build()
    {
        return $this->view('emails.unsupervisedStudents')
                    ->with('students', $this->unsupervisedStudents);
    }

}
