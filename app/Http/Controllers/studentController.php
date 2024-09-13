<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Project;
use App\Models\Complaint;
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
        $student = Auth::user(); 
        $studentId = $student->id;
         // Check if the user already has a project submitted
        $existingProject = Project::where('student_id', $studentId)->first();
        if ($existingProject) {
        return response()->json([
            'error' => 'You have already submitted your project. Please try to modify your existing project if needed.'
        ], 400); // 400 Bad Request
        }
        $validated = $request->validate([
            'title' => 'nullable|string|max:255', 
            'project_type' => 'required|string|max:255', 
            'partner' => 'nullable|string|max:255|unique:projects|required_if:project_type,binomial',
            'company' => 'nullable|string|max:255', 
            'teacher_email' => 'nullable|string|max:255',
            'specs' => 'nullable|file|mimes:pdf,doc,docx|max:2048', 
        ]);
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
            $file = $request->file('specs');
            $path = $request->file('specs')->store('files');
            $project->specs = $path;
            $project->original_filename = $file->getClientOriginalName();
            $project->save();  // Save the project again to store the file path
        }
        return response()->json(['success' => true, 'project' => $project], 201);
    } 
    public function show()
    {
        $student = Auth::user(); 
        $studentId = $student->id;
        $project = Project::where('student_id', $studentId)->first();
        return response()->json(['project' => $project], 200);
    }   
    public function update(Request $request)
{
    $student = Auth::user(); 
    $studentId = $student->id;
    $project = Project::where('student_id',$studentId)->first();
    if (!$project) {
        return response()->json(['error' => 'Project not found'], 404);
    }
    $project->project_title = $request->input('project_title', $project->project_title);
    $project->project_type = $request->input('project_type', $project->project_type);
    $project->teacher_email = $request->input('teacher_email', $project->teacher_email);
    $project->company = $request->input('company', $project->company);
    $project->partner = $request->input('partner', $project->partner);
    $res = $project->save();
    if ($res) {
        return response()->json(['success' => true, 'project' => $project], 200);
    } else {
        return response()->json(['error' => 'Failed to update project'], 500);
    }
}
public function uploadSpecs(Request $request)
{
    // Validate the request to ensure a file is provided
    $request->validate([
        'specs' => 'required|file|mimes:pdf,doc,docx', // Adjust the file types as needed
    ]);
    // Handle the file upload
    if ($request->hasFile('specs')) {
        $file = $request->file('specs');
        $fileName = $file->getClientOriginalName();
        // Store the file in the 'public/specs' directory
        $filePath = $file->store('public/specs');
        $student = Auth::user(); 
        $studentId = $student->id;
        $project = Project::where('student_id',$studentId)->first();
        $project->specs = $filePath;
        $project->original_filename = $fileName;
        $project->save();
        return response()->json(['file_path' => $filePath, 'message' => 'File uploaded successfully'], 200);
    }
        return response()->json(['message' => 'No file uploaded'], 400);
}
public function storeComplaint(Request $request)
    {
        $userId = $request->user()->id;

        if (!Complaint::canSubmitComplaint($userId)) {
            return response()->json(['error' => 'You can only submit one complaint per week.'], 403);
        }

        $complaint = Complaint::create([
            'user_id' => $userId,
            'complaint_type' => $request->input('complaint_type'),
            'object' => $request->input('object'),
            'message' => $request->input('message'),
            'status' => 'in_process',
        ]);

        return response()->json(['message' => 'Complaint submitted successfully.', 'complaint' => $complaint], 201);
    }
    public function checkEditFormComplaint() {
        $student = Auth::user(); 
        $studentId = $student->id;
        $complaint = Complaint::where('user_id', $studentId)
                              ->where('complaint_type', 'editForm')
                              ->first();
        if (!$complaint) {
          return response()->json(null, 200);
            }
        return response()->json([
                'status' => $complaint->status,
            ], 200);
    }
}