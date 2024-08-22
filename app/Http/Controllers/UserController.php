<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher ;
use App\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail; 
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function checkEmailExists(Request $request)
{
    $emailExists = User::where('email', $request->email)->exists();

    return response()->json(['exists' => $emailExists]);
}

public function sendVerificationCode(Request $request)
{
    $verificationCode = Str::random(6);  
    $encryptedCode = encrypt($verificationCode);
    Mail::to($request->email)->send(new VerificationEmail($verificationCode));
    
    return response()->json([
       'message' => 'Verification code sent',
       'encrypted_code' => $encryptedCode // Retourner le code au frontend pour stockage temporaire
    ]);
}

 public function verifyCode(Request $request)
{
    // Valider le code de vérification
    $request->validate([
        'code' => 'required|string|min:6|max:6',
    ]);
    
    $encryptedCode = $request->encrypted_code;
    try {
        $decryptedCode = decrypt($encryptedCode);
    } catch (DecryptException $e) {
        return response()->json(['success' => false, 'message' => 'Invalid verification code'], 400);
    }
       
    if ($request->code === $decryptedCode) {
        return response()->json(['success' => true, 
        'message' => 'Code verified successfully',
        'verification_code'=> $decryptedCode]);
    }
    return response()->json(['success' => false, 'message' => 'Invalid verification code'], 400);
} 

public function registerUser(Request $request)
{
    
    $role = $request->role;
    $baseRules = [
        'firstname' => 'required|string',
        'lastname' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|confirmed',
        'role' => 'required|in:student,teacher,admin',
    ];
    // Validation spécifique aux rôles
    $roleSpecificRules = [
    'cin' => 'required_if:role,student|unique:students,cin|max:8',
    'number' => 'required_if:role,student|integer|max:99999999',
    'specialty' => 'required_if:role,student|string',
    'status' => 'required_if:role,student|string',
    'grade' => 'required_if:role,teacher|string',
 ];
    // Fusionner les règles de validation
    $rules = array_merge($baseRules, $roleSpecificRules);
    $request->validate($rules); 

    $user = User::create([
        'name' => $request->firstname,
        'lastName' => $request->lastname,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role,
        'verification_code'=>$request->verification_code,
        'is_verified' => true,  
       
    ]);
    
    // Gérer les champs spécifiques selon le rôle
    if ($request->role === 'student') {
        Student::create([
            'student_id' => $user->id, 
            'first_name' => $request->firstname,
            'last_name' => $request->lastname,
            'cin' => $request->cin,
            'number' => $request->number,
            'specialty' => $request->specialty,
            'status' => $request->status,
            'email' => $user->email,
            'password' =>bcrypt($user->password),
        ]);
    }
    else Teacher::create([
        'teacher_id' => $user->id, 
        'first_name' => $request->firstname,
        'last_name' => $request->lastname,
        'grade' => $request->grade,
        'email' => $request->email,
        'password' =>bcrypt($request->password),
    ]);
    return response()->json([
        'message' => 'Registration successful',
        'role' => $user->role,
        
    ]);
}
public function login(Request $request)
{
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token=$user->createToken('authToken')->plainTextToken;
        return response()->json([
            'message' => 'Login successful',
            'role' => $user->role,
            'token' => $token, 
        ]);
    } else {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}

}
