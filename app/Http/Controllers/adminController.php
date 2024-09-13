<?php

namespace App\Http\Controllers;
use App\Models\Complaint;
use App\Models\teacherComplaint;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;

use Illuminate\Http\Request;

class adminController extends Controller
{
public function getComplaints()
{
    $complaints = Complaint::with('student:user_id,first_name,last_name,specialty,email')
                ->get(['id','user_id','complaint_type', 'object', 'message','status']);
    return response()->json($complaints);
}
public function getTeacherComplaints()
{
    $Tcomplaints = teacherComplaint::with('teacherComplaint:user_id,first_name,last_name')
                ->get(['user_id', 'object', 'message' ,'student_email','teacher_email']);
    return response()->json($Tcomplaints);
}
public function updateStatus(Request $request, $id)
    {
        // Validate status input
        $request->validate([
            'status' => 'required|in:approved,disapproved', // Allow only approved or disapproved values
        ]);
        $complaint = Complaint::findOrFail($id);
        $complaint->status = $request->status;
        $complaint->save();
        return response()->json(['message' => 'Status updated successfully', 'complaint' => $complaint], 200);
    }
    public function allUsers()
    {
        return User::where('role', '!=', 'admin')->get();  // Renvoie tous les utilisateurs
    }
    public function getUserDetails($id)
{
    $user = User::find($id);

    if ($user->role === 'student') {
        $studentDetails = Student::where('user_id', $id)->first();
        return response()->json([
            'user' => $user,
            'details' => $studentDetails,
            'type' => 'student',
        ]);
    } elseif ($user->role === 'teacher') {
        $teacherDetails = Teacher::where('user_id', $id)->first();
        return response()->json([
            'user' => $user,
            'details' => $teacherDetails,
            'type' => 'teacher',
        ]);
    }

    return response()->json(['message' => 'Role not found'], 404);
}
public function updateUser(Request $request, $id)
{
    // Trouver l'utilisateur
    $user = User::find($id);

    // Validation commune à tous les utilisateurs
    $request->validate([
        'firstname' => 'required',
        'lastname' => 'required',
        'email' => 'required|email',
        // Mot de passe facultatif, à valider uniquement s'il est fourni
        'password' => 'nullable|min:6|confirmed',
    ]);

    // Mettre à jour les champs communs
    $user->name = $request->firstname;
    $user->lastName = $request->lastname;
    $user->email = $request->email;

    // Vérification du rôle de l'utilisateur
    if ($user->role === 'student') {
        // Validation et mise à jour des champs spécifiques aux étudiants
        $request->validate([
            'cin' => 'required',
            'status' => 'required',
            'specialty' => 'required',
            'number' => 'required',
        ]);

        // Mettre à jour les informations de l'étudiant dans la table `students`
        $student = Student::where('user_id', $id)->first();
        $student = $user->student;
        $student->first_name = $request->firstname;
        $student->last_name = $request->lastname;
        $student->email = $request->email;
        $student->cin = $request->cin;
        $student->status = $request->status;
        $student->specialty = $request->specialty;
        $student->number = $request->number;
        $student->save();

    } elseif ($user->role === 'teacher') {
        // Validation et mise à jour des champs spécifiques aux enseignants
        $request->validate([
            'grade' => 'required',
        ]);

        // Mettre à jour les informations de l'enseignant dans la table `teachers`
        $teacher = Teacher::where('user_id', $id)->first();
        $teacher= $user->teacher;
        $teacher->first_name = $request->firstname;
        $teacher->last_name = $request->lastname;
        $teacher->email = $request->email;
        $teacher->grade = $request->grade;
        $teacher->save();
    }

    // Sauvegarder l'utilisateur mis à jour
    $user->save();

    return response()->json(['message' => 'User updated successfully']);
}

}

