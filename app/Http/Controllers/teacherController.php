<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Project;
use App\Models\Student;
use App\Models\teacherComplaint;
use App\Models\Availability;
use Illuminate\Support\Facades\Auth;

class teacherController extends Controller
{
    public function getTeacherEmails()
    {
        // Récupérer tous les enseignants et retourner uniquement leurs emails
        $emails = Teacher::pluck('email');
        return response()->json($emails);
    }
    public function getStudentsByTeacherEmail()
{
    $teacherEmail = Auth::user()->email;

    // Get students' IDs from projects where the teacher's email matches
    $studentIds = Project::where('teacher_email', $teacherEmail)->pluck('student_id');

    // Retrieve students with the corresponding user_ids from the students table
    $students = Student::whereIn('user_id', $studentIds)->get(['email','specialty']);

    return response()->json($students);
}
        
public function storeComplaint(Request $request)
    {
        $teacher = Auth::user()->id; 
        $request->validate([
            'teacher_email' => 'required|email',
            'student_email' => 'required|email',
            'object' => 'required|string',
            'message' => 'required|string',
        ]);

        $complaint = teacherComplaint::create([
            'user_id'=> $teacher,
            'teacher_email' => $request->input('teacher_email'),
            'student_email' => $request->input('student_email'),
            'object' => $request->input('object'),
            'message' => $request->input('message'),
        ]);

        return response()->json($complaint, 201);
    }
    public function getProjectDetailsByEmail(Request $request)
{
    $email = $request->input('email');
    // Find the student by email
    $student = Student::where('email', $email)->first();
    if ($student) {
        // Use the user_id (which is the same as student_id in the projects table)
        $project = Project::where('student_id', $student->user_id)->first();

        if ($project) {
            return response()->json([
                'company' => $project->company,
                'project_type' => $project->project_type,
                'partner' => $project->partner,
                'project_title' => $project->project_title,
                'specs' => url('storage/'. $project->specs),
                'original_filename' => $project->original_filename,
            ], 200);
        } else {
            return response()->json(['message' => 'No project found for this student'], 404);
        }
    } else {
        return response()->json(['message' => 'Student not found'], 404);
    }
}
public function store(Request $request)
{
    $teacherID = Auth::user()->id;
    $teacher=Teacher::where('user_id', $teacherID)->first();
    
    // Vérifier si l'enseignant a encore des disponibilités à ajouter
    if ($teacher->remaining_availabilities <= 0) {
        return response()->json([
            'message' => 'Vous avez déjà ajouté 4 disponibilités, vous ne pouvez pas en ajouter plus.'
        ], 403);
    }

    $validated = $request->validate([
        'date' => 'required|date',
        'start_time' => 'required',
        'end_time' => 'required',
        'status' => 'required|in:available,unavailable',
    ]);
    $validated['teacher_id'] = $teacherID;
    $availability = Availability::create($validated);

    $teacher->decrement('remaining_availabilities');

    return response()->json($availability, 201);
}
public function index()
{
    $teacherID = Auth::user()->id;
    $availabilities = Availability::where('teacher_id', $teacherID)->get();

    return response()->json($availabilities);
}
public function destroy($id)
{
    $teacherID = Auth::user()->id;
    $teacher=Teacher::where('user_id', $teacherID)->first();
    if ($teacher) {
        $teacher->remaining_availabilities++;
        $teacher->save();  // Save the changes to the database

        $availability = Availability::findOrFail($id);
        $availability->delete();

        return response()->json(['message' => 'Deleted successfully'], 204);
    }

    return response()->json(['message' => 'Teacher not found'], 404);
}
public function update(Request $request, $id)
{
    $availability = Availability::findOrFail($id);
    $availability->update($request->all());

    return response()->json($availability);
}
}
