<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher ;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail; 
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
        'role' => $role,
        'verification_code'=>$request->verification_code,
        'is_verified' => true,  
       
    ]);
    
    // Gérer les champs spécifiques selon le rôle
    if ($request->role === 'student') {
        Student::create([
            'user_id' => $user->id, 
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
    elseif ($request->role === 'teacher') {
     Teacher::create([
        'user_id' => $user->id, 
        'first_name' => $request->firstname,
        'last_name' => $request->lastname,
        'grade' => $request->grade,
        'email' => $request->email,
        'password' =>bcrypt($request->password),
    ]);
    }elseif ($request->role === 'admin') {
        $user->verification_code = null; 
        $user->save();
    }
    return response()->json([
        'message' => 'Registration successful',   
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
            'email'=> $user->email,
            'token' => $token, 
            'userDetails' => [
                'firstName' => $user->name, 
                'lastName' => $user->lastName   
            ]
        ]);
    } else {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
public function getUser()
{
    // Récupérer l'utilisateur authentifié
    $user = Auth::user();
    
    // Si c'est un étudiant, on récupère aussi les informations supplémentaires depuis la table "students"
    if ($user->role === 'student') {
        $student = $user->student; // Relation one-to-one avec le modèle Student
        $user->cin = $student->cin;
        $user->status = $student->status;
        $user->specialty = $student->specialty;
        $user->number = $student->number;
        
    } elseif ($user->role === 'teacher') {
        $teacher = $user->teacher; // Relation one-to-one avec le modèle Teacher
        $user->grade = $teacher->grade;
        
    }


    // Renvoyer les informations de l'utilisateur au format JSON
    return response()->json($user);
}
public function updateUser(Request $request)
    {
        $user = Auth::user();
        // Valider les informations du formulaire
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'cin' => 'required_if:role,student|string|max:8',
            'status' => 'required_if:role,student|string|max:255',
            'specialty' => 'required_if:role,student|string|max:255',
            'grade' => 'required_if:role,teacher|string|max:255',
            'number' => 'required_if:role,student|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Mettre à jour les informations utilisateur
        $user->name = $request->firstname;
        $user->lastName = $request->lastname;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);

        $user->save();

        // Si c'est un étudiant, mettre à jour les informations spécifiques de la table "students"
        if ($user->role === 'student') {
            $student = $user->student; // Relation one-to-one avec le modèle Student
            $student->first_name = $request->firstname;
            $student->last_name = $request->lastname;
            $student->cin = $request->cin;
            $student->status = $request->status;
            $student->specialty = $request->specialty;
            $student->number = $request->number;
            $student->save();
        }elseif ($user->role === 'teacher') {
            $teacher= $user->teacher;
            $teacher->first_name = $request->firstname;
            $teacher->last_name = $request->lastname;
            $teacher->grade = $request->grade;
            $teacher->email = $request->email;
            $teacher->save();
        }


        return response()->json(['message' => 'User updated successfully']);
    }
}
