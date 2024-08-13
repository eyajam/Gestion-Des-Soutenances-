<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    
    public function storeStudent(Request $request)
{
    $request->validate([
        'firstname' => 'required',
        'lastname' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|confirmed',
        'role'=> 'required|in:student,teacher,admin',
        'CIN' => 'required|unique:users,CIN|max:8'
    ]);

    $user = User::create([
        'firstname' => $request->name,
        'lastname' => $request->lastName,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role'=> $request->role,
        'verification_code' => Str::random(8), 
        'is_verified' => false
    ]);

    Student::create([
        'student_id' => $user->id, 
        'firstname' => $request->first_name,
        'lastname' => $request->last_name,
        'email' => $request->email,
        'password' =>bcrypt($request->password),
        'CIN' => $request->CIN
    ]);

    $user->notify(new VerifyEmail()); // Envoyer l'email de vérification

    return response()->json(['message' => 'Please check your email to verify your account!'], 201);
}

public function storeTeacher(Request $request){
         // la mm logique que student dans table users et teachers

    $user->notify(new VerifyEmail()); // Envoyer l'email de vérification

    return response()->json(['message' => 'Please check your email to verify your account!'], 201);    
}

public function storeUser(Request $request)
{
$role = $request->input('role');

    switch ($role) {
        case 'student':
            return $this->storeStudent($request);
        case 'teacher':
            return $this->storeTeacher($request);
        default:
            return response()->json(['error' => 'Invalid role specified'], 400);
    }
}

}