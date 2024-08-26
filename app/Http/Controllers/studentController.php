<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class studentController extends Controller
{
    public function checkCinExists(Request $request)
{
    $cinExists = Student::where('cin', $request->cin)->exists();
    return response()->json(['exists' => $cinExists]);
}
public function getLoggedInStudent(Request $request)
{
    $email = $request->input('email'); 
    // Find the student by the provided email
    $student = Student::where('email', $email)->first();
    if (!$student) {
        return response()->json(['error' => 'Student not found'], 404); // Not Found if no student is found
    }
    $specialty = $student->specialty;
    return response()->json(['specialty' => $specialty]);
}
public function getStudentsBySpecialty(Request $request)
    {
        $specialty = $request->input('specialty');
        $email = $request->input('email'); // Current student's email
        try {
            $students = Student::where('specialty', $specialty)
                ->where('email', '!=', $email)
                ->pluck('email');
            return response()->json($students, 200); // Return JSON response with a 200 status code
        } catch (\Exception $e) {
            // Handle the error and return a JSON response with a proper error message and status code
            return response()->json(['error' => 'Failed to retrieve students.'], 500);
        }
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255', // Nullable, pas besoin de "required"
            'project_type' => 'required|string|max:255', // Required
            'partner' => 'required|string|max:255|unique:projects', // Required et unique
            'company' => 'nullable|string|max:255', // Nullable, donc pas de "required"
            'teacher_email' => 'nullable|string|max:255', // Nullable
            'specs' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Nullable et fichier
        ]);
        $student = Auth::user(); 
        $studentId = $student->id;
        // Traitement et sauvegarde des données
        $project = Project::create([
            'student_id' => $studentId,
            'project_title' => $request->title,
            'project_type' => $request->project_type,
            'partner' => $request->partner,
            'company' => $request->company,
            'teacher_email' => $request->teacher_email,
        ]);
        if ($request->hasFile('specs')) {
            $path = $request->file('specs')->store('public/files');
            $project->specs = $path;
            $project->save();  // Save the project again to store the file path
        }
        return response()->json(['success' => true, 'project' => $project], 201);
    }    
   
}
