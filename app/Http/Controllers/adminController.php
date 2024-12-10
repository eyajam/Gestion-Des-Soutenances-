<?php

namespace App\Http\Controllers;
use App\Models\Complaint;
use App\Models\teacherComplaint;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Defense;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\UnsupervisedStudentsMail; 
use App\Models\UnsupervisedStudentPeriod;
use Carbon\Carbon;
use App\Models\Project;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmailsImport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AdminScheduleExport;
use Illuminate\Support\Facades\Log;

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
    $Tcomplaints = teacherComplaint::with('teacherComplaint:user_id,first_name,last_name','student:email,session')
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
        return User::where('role', '!=', 'admin')->get();  // Renvoie tous les utilisateurs sauf admin
    }
    public function getUserDetails($id)
{
    $user = User::find($id);
     // Tentative de décryptage du mot de passe si possible
     $decryptedPassword = null;
    
     try {
         $decryptedPassword = Crypt::decryptString($user->password);
         if ($this->isSerialized($decryptedPassword)) {
            $decryptedPassword = unserialize($decryptedPassword);
        }
     } catch (\Exception $e) {
         // Si le mot de passe est haché, on ne fait rien
     }
    if ($user->role === 'student') {
        $studentDetails = Student::where('user_id', $id)->first();
        return response()->json([
            'user' => $user,
            'details' => $studentDetails,
            'type' => 'student',
            'password' => $decryptedPassword,
        ]);
    } elseif ($user->role === 'teacher') {
        $teacherDetails = Teacher::where('user_id', $id)->first();
        return response()->json([
            'user' => $user,
            'details' => $teacherDetails,
            'type' => 'teacher',
            'password' => $decryptedPassword,
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
        // s'il est fourni
        'password' => 'sometimes|nullable|max:8',
    ]);

    // Mettre à jour les champs communs
    $user->name = $request->firstname;
    $user->lastName = $request->lastname;
    $user->email = $request->email;
    
        // Si un mot de passe est fourni, nous assumons qu'il doit être crypté
        $user->password = encrypt($request->password);
    
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
        $student->password = encrypt($request->password);
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
        $teacher->password = encrypt($request->password);
        $teacher->save();
    }

    // Sauvegarder l'utilisateur mis à jour
    $user->save();

    return response()->json(['message' => 'User updated successfully']);
}
public function deleteUsers(Request $request)
{
    $userIds = $request->input('ids');
    
    // Delete users where ID is in the array
    User::whereIn('id', $userIds)->delete();

    return response()->json(['message' => 'Users deleted successfully'], 200);
}
public function addUser(Request $request)
{
    // Validate the common fields
    $validatedData = $request->validate([
        'firstname' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|max:8',
        'type' => 'required|in:teacher,student', // Ensure type is either teacher or student
    ]);

    if ($request->type === 'teacher') {
        // Validate additional fields for teachers
        $validatedData = array_merge($validatedData, $request->validate([
            'grade' => 'required|string|max:255',
        ]));
    } elseif ($request->type === 'student') {
        // Validate additional fields for students
        $validatedData = array_merge($validatedData, $request->validate([
            'cin' => 'required|string|max:255',
            'specialty' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:8', // Assuming number is required for students initially.
        ]));
    }

    // Create the user in the database
    $user = User::create([
        'name' => $validatedData['firstname'],
        'lastName' => $validatedData['lastname'],
        'email' => $validatedData['email'],
        'password' => encrypt($validatedData['password']),
        'role' => $request->type, // Store the role ('teacher' or 'student')
    ]);

    // If the user is a teacher or student, store additional data in their respective tables
    if ($request->type === 'teacher') {
        $grade = strtolower($validatedData['grade']);
        $isPresident = false;
        $isRapporteur = false;

        if (in_array($grade, ['maitre assistant', 'professeur', 'maitre de conf'])) {
            $isPresident = true;
            $isRapporteur = true;
        } elseif (in_array($grade, ['assistant', 'vacataire','PES','contractuel'])) {
            $isRapporteur = true;
        }
        Teacher::create([
            'user_id' => $user->id,
            'first_name' => $validatedData['firstname'],
            'last_name' => $validatedData['lastname'],
            'email' => $validatedData['email'],
            'password' => encrypt($validatedData['password']),
            'grade' => $validatedData['grade'],
            'is_president' => $isPresident,
            'is_rapporteur' => $isRapporteur,
        ]);
    } elseif ($request->type === 'student') {
        Student::create([
            'user_id' => $user->id,
            'first_name' => $validatedData['firstname'],
            'last_name' => $validatedData['lastname'],
            'email' => $validatedData['email'],
            'password' => encrypt($validatedData['password']),
            'cin' => $validatedData['cin'],
            'specialty' => $validatedData['specialty'],
            'status' => $validatedData['status'],
            'number' => $validatedData['phoneNumber'], // Assuming number is not provided for students initially.
        ]);
    }

    return response()->json(['message' => 'User added successfully'], 200);
}
private function isSerialized($data)
{
    return $data == serialize(false) || @unserialize($data) !== false ;
}
public function getStudentsWithoutSupervision() {
    $students = DB::table('projects')
        ->whereNull('teacher_email') // Only select projects without supervision
        ->join('students', 'students.user_id', '=', 'projects.student_id')
        ->select('students.first_name', 'students.last_name', 'students.cin', 'students.specialty', 'projects.project_type','projects.student_id','projects.partner')
        ->get();

    return response()->json($students);
}
public function getTeachersWithProjectCount() {
    $teachers = DB::table('teachers')
        ->leftJoin('projects', 'teachers.email', '=', 'projects.teacher_email')
        ->select(
            'teachers.first_name', 
            'teachers.last_name', 
            'teachers.grade', 
            'teachers.email',
            DB::raw('CAST(SUM(CASE WHEN project_type = "monomial" THEN 1 WHEN project_type = "binomial" THEN 0.5 END) AS UNSIGNED) as project_count')
        )
        ->groupBy('teachers.email', 'teachers.first_name', 'teachers.last_name', 'teachers.grade')
        ->get();

    return response()->json($teachers);
}

public function sendUnsupervisedStudentsListToTeachers() {
    // Fetch unsupervised students
    $unsupervisedStudents = DB::table('projects')
        ->whereNull('teacher_email')
        ->join('students', 'students.user_id', '=', 'projects.student_id')
        ->select('students.first_name', 'students.last_name', 'students.cin', 'students.specialty', 'projects.project_type')
        ->get();

        $startDate = Carbon::now();
        $endDate = $startDate->addWeeks(2);
    
        UnsupervisedStudentPeriod::create([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'open',  // Status can be 'open' untill the period ends
        ]);

    $teachers = DB::table('teachers')->select('email')->get();
    foreach ($teachers as $teacher) {
        // Example of sending an email to each teacher
        Mail::to($teacher->email)->send(new UnsupervisedStudentsMail($unsupervisedStudents));
    }

    return response()->json(['message' => 'Request sent to all teachers.']);
}
// In your controller
public function getDeadline()
{
    $period = DB::table('unsupervised_student_periods')->where('status', 'open')->first();

    if ($period) {
        return response()->json([
            'end_date' => $period->end_date,
            'status' => 'open'
        ]);
    }
    return response()->json(['status'=>'closed']);
}
public function getUnsupervisedProjects()
{
    $unsupervisedProjects = Project::whereNull('teacher_email')
        ->join('students', 'projects.student_id', '=', 'students.user_id') // Jointure avec la table students
        ->select('projects.*', 'students.specialty') // Sélectionner les champs des projets et la spécialité de l'étudiant
        ->get();

    return response()->json($unsupervisedProjects);
}
public function assignProjectsToTeacher(Request $request)
{
    // Valider les données reçues
    $request->validate([
        'teacher' => 'required|email', // Email de l'enseignant
        'student_ids' => 'required|array', // Liste des IDs des étudiants
        'student_ids.*' => 'exists:students,user_id', // Vérifier que chaque ID correspond à un étudiant existant
    ]);
    $teacherEmail = $request->input('teacher');
    
    $studentIds = $request->input('student_ids');

    $projectGroups = Project::whereIn('student_id', $studentIds)
        ->pluck('project_group');  // Récupérer tous les project_group associés

    Project::whereIn('project_group', $projectGroups)
        ->update(['teacher_email' => $teacherEmail]); // Mettre à jour le champ teacher_email pour tous les étudiants dans ces groupes

    return response()->json(['message' => 'Projects successfully assigned to teacher.']);
}
public function stopSupervisionPeriod(Request $request)
{
    // Mettre à jour le statut de la période
    $period = UnsupervisedStudentPeriod::where('status', 'open')->first();

    if ($period) {
        $period->status = 'closed';
        $period->save();
        return response()->json(['message' => 'Supervision period stopped successfully.']);
    }

    return response()->json(['error' => 'No active period found.'], 404);
}
public function updateSession(Request $request)
{
    $studEmail = $request->input('email');
    $student = Student::where('email', $studEmail)->first();

    if ($student) {
        // Mettre à jour le champ session à 'second'
        $student->session = 'second';
        $student->save();

        return response()->json(['message' => 'Session updated to second', 'session' => $student->session]);
    }

    return response()->json(['error' => 'Student not found'], 404);
}
////////////////////////////////
    public function getStatistics()
    {
        $total_students = DB::table('students')->count();

        $students_filled = DB::table('projects')
                             ->distinct()
                             ->count('student_id');

        $binomial_students = DB::table('projects')
                               ->where('project_type', 'binomial')
                               ->distinct()
                               ->count('student_id');

        $students_without_project = DB::table('projects')
                                      ->whereNull('project_title')
                                      ->whereNull('company')
                                      ->distinct()
                                      ->count('student_id');

        $students_with_supervisor = DB::table('projects')
                                      ->whereNotNull('teacher_email')
                                      ->distinct()
                                      ->count('student_id');

        $students_without_supervisor = DB::table('projects')
                                         ->whereNull('teacher_email')
                                         ->distinct()
                                         ->count('student_id');

        $sessions = DB::table('students')
                      ->select('session', DB::raw('count(*) as total'))
                      ->groupBy('session')
                      ->get();

        return response()->json([
            'total_students' => $total_students,
            'filled_percentage' => ($students_filled / $total_students) * 100,
            'binomial_percentage' => ($binomial_students / $total_students) * 100,
            'without_project_percentage' => ($students_without_project / $total_students) * 100,
            'with_supervisor_percentage' => ($students_with_supervisor / $total_students) * 100,
            'without_supervisor_percentage' => ($students_without_supervisor / $total_students) * 100,
            'sessions' => $sessions
        ]);
    }
     public function verifyDeposit(Request $request)
{
    // Importer les emails depuis le fichier Excel
    $importedEmails = Excel::toCollection(new EmailsImport, $request->file('file'))[0]
    ->pluck('email')
    ->map(function($email) {
        return strtolower(trim($email));  // Enlever les espaces et convertir en minuscules
    })
    ->toArray();
    
    // Récupérer les emails groupés par project_group depuis la base de données
    $studentsByGroup = DB::table('students')
    ->join('projects', 'students.user_id', '=', 'projects.student_id')  // Jointure entre les tables students et projects
    ->select('projects.project_group', DB::raw('GROUP_CONCAT(LOWER(TRIM(students.email))) as emails'))  // Sélectionner project_group et concaténer les emails
    ->groupBy('projects.project_group')  // Grouper par project_group
    ->get();

    $missingReports = [];

    // Vérifier pour chaque groupe s'ils ont tous déposé
    foreach ($studentsByGroup as $group) {
        $groupEmails = explode(',', $group->emails);
        $groupDeposited = array_intersect($groupEmails, $importedEmails);// est ce que les emails mawjodin fi les 2 arrays wala le

        // Si un étudiant dans le binôme n'a pas déposé, on le note avec l'email de son binôme
        if (count($groupEmails) > 1) { // Binôme
            if (count($groupDeposited) == 1) {
                $missingReports[] = [
                    'student_email' => implode(',', array_diff($groupEmails, $groupDeposited)), //eneho el email li moch mawjoud
                    'binome_email' => implode(',', $groupDeposited),
                    'project_group' => $group->project_group,
                ];
            } elseif (count($groupDeposited) == 0) {
                $missingReports[] = [
                    'student_email' => implode(',', $groupEmails),
                    'binome_email' => null,
                    'project_group' => $group->project_group,
                ];
            }
        } else { // Monôme
            if (count($groupDeposited) == 0) {
                $missingReports[] = [
                    'student_email' => $groupEmails[0],
                    'binome_email' => null,
                    'project_group' => $group->project_group,
                ];
            }
        }
    }
    $fileContent = "Missing Reports:\n\n";
    foreach ($missingReports as $report) {
        $fileContent .= "Project Group: " . $report['project_group'] . "\n";
        $fileContent .= "Student Email who has not deposit: " . $report['student_email'] . "\n";
        if ($report['binome_email']) {
            $fileContent .= "Binome Email: " . $report['binome_email'] . "\n";
        }
        $fileContent .= "\n";
    }

    // Créer un fichier .txt temporaire
    $fileName = 'missing_reports.txt';
    $filePath = storage_path($fileName);
    file_put_contents($filePath, $fileContent);

    // Retourner le fichier .txt pour téléchargement
    return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
}  

public function teachers()
    {

        $teachers = Teacher::all();
        return response()->json($teachers);
    }
    public function teachersPlanningType(Request $request)
{
    // Récupérer la liste des enseignants depuis la requête
    $teachers = $request->input('teachers');

    // Array pour stocker les informations finales
    $rolesAndDefencesTeachers = [];

    foreach ($teachers as $teacher) {
        // Récupérer les informations spécifiques pour chaque enseignant
        $userId = $teacher['user_id'];
        $planningType = $teacher['planningType'];
        $isPresident = isset($teacher['isPresident']) ? $teacher['isPresident'] : false;
        $isRapporteur = isset($teacher['isRapporteur']) ? $teacher['isRapporteur'] : false;
        $dpCount = isset($teacher['DP']) ? $teacher['DP'] : 0;
        $drCount = isset($teacher['DR']) ? $teacher['DR'] : 0;

        // Récupérer l'enseignant depuis la base de données
        $teacherModel = Teacher::find($userId);

        // Vérifier si l'enseignant existe
        if ($teacherModel) {
            // Si l'enseignant est marqué comme président et que ce n'est pas déjà le cas dans la base de données
            if ($isPresident && !$teacherModel->is_president) {
                $teacherModel->is_president = 1;
                $teacherModel->save();
            }

            // Si l'enseignant est marqué comme rapporteur et que ce n'est pas déjà le cas dans la base de données
            if ($isRapporteur && !$teacherModel->is_rapporteur) {
                $teacherModel->is_rapporteur = 1;
                $teacherModel->save();
            }

            // Ajouter les informations au tableau final
            $rolesAndDefencesTeachers[$userId] = [
                'planningType' => $planningType,
                'is_president' => $teacherModel->is_president,
                'is_rapporteur' => $teacherModel->is_rapporteur,
                'dpCount' => $dpCount,  // Déjà récupéré du frontend
                'drCount' => $drCount   // Déjà récupéré du frontend
            ];
        } else {
            // Si l'enseignant n'existe pas, ajouter un message d'erreur ou gérer l'absence
            $rolesAndDefencesTeachers[$userId] = [
                'error' => "Teacher with user_id $userId not found",
            ];
        }
    }

    // Retourner le tableau des informations sous format JSON
    return response()->json($rolesAndDefencesTeachers);
}

    private function runSchedulingAlgorithm($classrooms, $block, $rolesAndDefencesTeachers)
{
    $schedule = [];
    $schedule_teachers = [];  
    $schedule_admin = [];
    $projects = Project::all();
    $hours = ['8:00', '9:00', '10:00', '12:00', '13:00', '14:00', '15:00']; // Skipping 11:00 for break
    $teacherDefenseCount = [];
    $teacherMaxForDay = [];  // New array to track if a teacher reached their max for the day

    // Helper function to determine max defenses per block based on planning type
    function getMaxDefensesPerDay($planningType)
    {
        return $planningType === 'successive' ? 6 : 4;
          
    }

    // Function to find available teachers with priority logic
    function findAvailableTeacherWithPriority($role, $date, $hour, $assignedTeachers, $planningTypes, $excludeTeachers = [])
{
    $excludedTeachers = array_merge($assignedTeachers, $excludeTeachers);

    // First, find teachers with `planningType=successive`
    $successiveTeachers = Teacher::where(function ($query) use ($date, $hour) {
        $query->whereHas('availabilities', function ($availabilityQuery) use ($date, $hour) {
            $availabilityQuery->where('date', $date)
                              ->where(function ($subQuery) use ($hour) {
                                  // Case 1: Teacher has explicitly marked themselves as available for certain hours
                                  $subQuery->where('status', 'available')
                                           ->where('start_time', '<=', $hour)
                                           ->where('end_time', '>=', $hour)
                                  // Case 2: Teacher has marked themselves unavailable, they are available outside this range
                                           ->orWhere(function ($unavailableQuery) use ($hour) {
                                               $unavailableQuery->where('status', 'unavailable')
                                                                ->where(function ($timeQuery) use ($hour) {
                                                                    $timeQuery->where('start_time', '>', $hour)
                                                                              ->orWhere('end_time', '<', $hour);
                                                                });
                                           });
                              });
        })
        ->orWhereDoesntHave('availabilities', function ($availabilityQuery) use ($date) {
            // Case 3: Teachers with no availability records are assumed to be available all day
            $availabilityQuery->where('date', $date);
        });
    })
    ->where("is_{$role}", 1)
    ->whereNotIn('user_id', $excludedTeachers)
    ->get()
    ->filter(function ($teacher) use ($planningTypes) {
        return $planningTypes[$teacher->user_id] === 'successive';
    })
    ->shuffle();

    if ($successiveTeachers->isNotEmpty()) {
        return $successiveTeachers;
    }

    // If no `successive` teachers are available, find any other available teacher
    $availableTeachers = Teacher::where(function ($query) use ($date, $hour) {
        $query->whereHas('availabilities', function ($availabilityQuery) use ($date, $hour) {
            $availabilityQuery->where('date', $date)
                              ->where(function ($subQuery) use ($hour) {
                                  $subQuery->where('status', 'available')
                                           ->where('start_time', '<=', $hour)
                                           ->where('end_time', '>=', $hour)
                                  ->orWhere(function ($unavailableQuery) use ($hour) {
                                      $unavailableQuery->where('status', 'unavailable')
                                                       ->where(function ($timeQuery) use ($hour) {
                                                           $timeQuery->where('start_time', '>', $hour)
                                                                     ->orWhere('end_time', '<', $hour);
                                                       });
                                  });
                              });
        })
        ->orWhereDoesntHave('availabilities', function ($availabilityQuery) use ($date) {
            $availabilityQuery->where('date', $date);
        });
    })
    ->where("is_{$role}", 1)
    ->whereNotIn('user_id', $excludedTeachers)
    ->get()
    ->shuffle();
    

    return $availableTeachers;
}
function findEncadrantWithAvailability($project, $date, $hour,$planningTypes ,$assignedTeachers = [])
{
    // Récupérer l'encadrant en fonction de l'email lié au projet
    $encadrantSuccessive = Teacher::where('email', $project->teacher_email)
        ->whereNotIn('user_id', array_merge($assignedTeachers)) // Exclure les enseignants déjà assignés
        ->where(function ($query) use ($date, $hour) {
            // Vérifier si l'encadrant est disponible à la date et l'heure spécifiées
            $query->whereHas('availabilities', function ($availabilityQuery) use ($date, $hour) {
                $availabilityQuery->where('date', $date)
                ->where(function ($subQuery) use ($hour) {
                    $subQuery->where('status', 'available')
                             ->where('start_time', '<=', $hour)
                             ->where('end_time', '>=', $hour)
                    ->orWhere(function ($unavailableQuery) use ($hour) {
                        $unavailableQuery->where('status', 'unavailable')
                                         ->where(function ($timeQuery) use ($hour) {
                                             $timeQuery->where('start_time', '>', $hour)
                                                       ->orWhere('end_time', '<', $hour);
                                         });
                    });
                });
            })
            // Si l'encadrant n'a pas de disponibilités spécifiques, il est considéré disponible toute la journée
            ->orWhereDoesntHave('availabilities', function ($availabilityQuery) use ($date) {
                $availabilityQuery->where('date', $date);
            });
        })
        ->get()
        ->filter(function ($teacher) use ($planningTypes) {
            return $planningTypes[$teacher->user_id] === 'successive';
        })
        ->shuffle();
        if ($encadrantSuccessive->isNotEmpty()) {
            return $encadrantSuccessive;
        }
        // Si aucun encadrant `successive` n'est disponible, rechercher un autre encadrant
        $encadrantNS = Teacher::where('email', $project->teacher_email)
        ->whereNotIn('user_id', array_merge($assignedTeachers)) // Exclure les enseignants déjà assignés
        ->where(function ($query) use ($date, $hour) {
            // Vérifier si l'encadrant est disponible à la date et l'heure spécifiées
            $query->whereHas('availabilities', function ($availabilityQuery) use ($date, $hour) {
                $availabilityQuery->where('date', $date)
                ->where(function ($subQuery) use ($hour) {
                    $subQuery->where('status', 'available')
                             ->where('start_time', '<=', $hour)
                             ->where('end_time', '>=', $hour)
                    ->orWhere(function ($unavailableQuery) use ($hour) {
                        $unavailableQuery->where('status', 'unavailable')
                                         ->where(function ($timeQuery) use ($hour) {
                                             $timeQuery->where('start_time', '>', $hour)
                                                       ->orWhere('end_time', '<', $hour);
                                         });
                    });
                });
            })
            // Si l'encadrant n'a pas de disponibilités spécifiques, il est considéré disponible toute la journée
            ->orWhereDoesntHave('availabilities', function ($availabilityQuery) use ($date) {
                $availabilityQuery->where('date', $date);
            });
        })
        ->get()
        ->shuffle();

    return $encadrantNS;
}


    // Helper function to get student names
    function getStudentDetails($project)
    {
        $student = Student::where('user_id', $project->student_id)->first();
        $names = "{$student->first_name} {$student->last_name}";
        $cins   ="{$student->cin}";
        $emails ="{$student->email}";
        $title = "{$project->project_title}";
        $specialty = "{$student->specialty}";

        $isBinomial = Project::where('project_group', $project->project_group)
                             ->where('student_id', '!=', $project->student_id)
                             ->exists();

        if ($isBinomial) {
            $partnerProject = Project::where('project_group', $project->project_group)
                                     ->where('student_id', '!=', $project->student_id)
                                     ->first();
            if ($partnerProject) {
                $partner = Student::where('user_id', $partnerProject->student_id)->first();
                $names .= ", {$partner->first_name} {$partner->last_name}";
                $cins .= ", {$partner->cin}";
                $emails.= ", {$partner->email}";
            }
        }

        return [
            'names' => $names,
            'cins' => $cins,
            'emails' => $emails,
            'title' => $title,
            'specialty' => $specialty,
        ];
    }
    function getStudentDetails2($project)
    {
       // Get the main student's details
    $student = Student::where('user_id', $project->student_id)->first();
    $name = "{$student->first_name} {$student->last_name}";
    $cin = "{$student->cin}";

    // Initialize an array to hold the student details
    $studentsDetails = [
        [
            'name' => $name,
            'cin' => $cin,
        ]
    ];

    // Check if the project is binomial
    $isBinomial = Project::where('project_group', $project->project_group)
                         ->where('student_id', '!=', $project->student_id)
                         ->exists();

    if ($isBinomial) {
        $partnerProject = Project::where('project_group', $project->project_group)
                                 ->where('student_id', '!=', $project->student_id)
                                 ->first();
        if ($partnerProject) {
            $partner = Student::where('user_id', $partnerProject->student_id)->first();
            $studentsDetails[] = [
                'name' => "{$partner->first_name} {$partner->last_name}",
                'cin' => "{$partner->cin}",
            ];
        }
    }

    return $studentsDetails;
    }

    function TeacherDefenses(&$teacherData, $role)
    {
        if ($role === 'president' && $teacherData['dpCount'] > 0) {
            $teacherData['dpCount']--;
            if ($teacherData['dpCount'] == 0) {
                $teacherData['is_president'] = false;
            }
            return true;
        }

        if ($role === 'rapporteur' && $teacherData['drCount'] > 0) {
            $teacherData['drCount']--;
            if ($teacherData['drCount'] == 0) {
                $teacherData['is_rapporteur'] = false;
            }
            return true;
        }

        return false;
    }

    foreach (range(1, 30) as $day) {
        $currentDate = "2024-06-{$day}";
        $teacherDefenseCount[$currentDate] = [];
        $teacherMaxForDay[$currentDate] = [];  // Track teachers who reached max defenses for the day

        foreach ($hours as $hour) {
            $availableClassrooms = range(1, (int)$classrooms);
            $hourlyAssignedTeachers = [];

            foreach ($availableClassrooms as $key => $classroom) {
                $project = $projects->first();
                if (!$project) break;

                // Determine the current block: Block 1 (8:00 - 10:00) or Block 2 (12:00 - 15:00)
                $blockNumber = $hour < '12:00' ? 1 : 2;

                // Find the Encadrant for this project
                $encadrants = findEncadrantWithAvailability($project, $currentDate, $hour, $rolesAndDefencesTeachers, $hourlyAssignedTeachers);
                $encadrant_t=null;
                foreach ($encadrants as $encadrant) {
                    if (!in_array($encadrant->user_id, $hourlyAssignedTeachers)) {
                        $encadrant_t = $encadrant;
                        Log::info("Encadrant trouvé : ", [$encadrant_t]);
                        break;
                    }
                }
                if ($encadrant_t && !in_array($encadrant_t->user_id, $hourlyAssignedTeachers)) {
                    $encadrantPlanningType = $rolesAndDefencesTeachers[$encadrant_t->user_id]['planningType'] ?? 'unsuccessive';
                    $maxDefensesPerBlock = getMaxDefensesPerDay($encadrantPlanningType);

                    if (!isset($teacherDefenseCount[$currentDate][$encadrant_t->user_id])) {
                        $teacherDefenseCount[$currentDate][$encadrant_t->user_id] = 0;
                    }

                    // Block constraints: No more than 3 defenses in Block 1
                    if ($blockNumber === 1 && $teacherDefenseCount[$currentDate][$encadrant_t->user_id] >= 3) {
                        continue ; // Skip this teacher for Block 1
                    }

                    // Block 2 constraints based on planningType
                    if ($blockNumber === 2 && $teacherDefenseCount[$currentDate][$encadrant_t->user_id] >= $maxDefensesPerBlock) {
                        $teacherMaxForDay[$currentDate][$encadrant_t->user_id] = true;  // Mark teacher as maxed for the day
                        continue ; // Skip this teacher if they've reached their daily limit
                    }

                    if (isset($teacherMaxForDay[$currentDate][$encadrant_t->user_id])) {
                        continue ;  // Skip this teacher for the rest of the day
                    }
        
                      $hourlyAssignedTeachers[] = $encadrant_t->user_id;
                      $teacherDefenseCount[$currentDate][$encadrant_t->user_id]++;

                   } else {continue; }

                    // Find President (same logic as Encadrant)
                    $presidents = findAvailableTeacherWithPriority('president', $currentDate, $hour, $hourlyAssignedTeachers, $rolesAndDefencesTeachers, [$encadrant->user_id]);
                    $president_t=null;
                    foreach ($presidents as $president) {
                        if (!in_array($president->user_id, $hourlyAssignedTeachers)) {
                            $president_t = $president;
                            Log::info("president trouvé : ", [$president_t]);
                            break;
                        }
                    }

                    if ($president_t && !in_array($president_t->user_id, $hourlyAssignedTeachers)) {
                        $presidentPlanningType = $rolesAndDefencesTeachers[$president_t->user_id]['planningType'] ?? 'unsuccessive';
                        $maxDefensesPerBlockPresident = getMaxDefensesPerDay($presidentPlanningType);
                        $president_user_id = $president_t->user_id;

                        if (!isset($teacherDefenseCount[$currentDate][$president_t->user_id])) {
                            $teacherDefenseCount[$currentDate][$president_t->user_id] = 0;
                        }

                        if ($blockNumber === 1 && $teacherDefenseCount[$currentDate][$president_t->user_id] >= 3) {
                            continue ;
                        }

                        if ($blockNumber === 2 && $teacherDefenseCount[$currentDate][$president_t->user_id] >= $maxDefensesPerBlockPresident) {
                            $teacherMaxForDay[$currentDate][$president_t->user_id] = true;
                            continue ;
                        }

                        if (isset($teacherMaxForDay[$currentDate][$president_t->user_id])) {
                            continue ;
                        }

                        if (isset($rolesAndDefencesTeachers[$president_user_id])) {
        // Assign the president role using the assignTeacherRole function
                         if (TeacherDefenses($rolesAndDefencesTeachers[$president_user_id], 'president')) {
                           $hourlyAssignedTeachers[] = $president_user_id;
                           $teacherDefenseCount[$currentDate][$president_user_id]++;
                         } else { continue;} 
                         } else { continue; }
                    }
                  else {continue ;}

                    // Find Rapporteur (same logic as President and Encadrant)
                    $rapporteurs = findAvailableTeacherWithPriority('rapporteur', $currentDate, $hour, $hourlyAssignedTeachers, $rolesAndDefencesTeachers, [$encadrant->user_id, $president_t->user_id]);
                    $rapporteur_t=null;
                    foreach ($rapporteurs as $rapporteur) {
                        if (!in_array($rapporteur->user_id, $hourlyAssignedTeachers)) {
                            $rapporteur_t = $rapporteur;
                            Log::info("rapporteur trouvé : ", [$rapporteur_t]);
                            break;
                        }
                    }

                    if ($rapporteur_t && !in_array($rapporteur_t->user_id, $hourlyAssignedTeachers)) {
                        $rapporteurPlanningType = $rolesAndDefencesTeachers[$rapporteur_t->user_id]['planningType'] ?? 'unsuccessive';
                        $maxDefensesPerBlockRapporteur = getMaxDefensesPerDay($rapporteurPlanningType);
                        $rapporteur_user_id = $rapporteur_t->user_id;

                        if (!isset($teacherDefenseCount[$currentDate][$rapporteur_t->user_id])) {
                            $teacherDefenseCount[$currentDate][$rapporteur_t->user_id] = 0;
                        }

                        if ($blockNumber === 1 && $teacherDefenseCount[$currentDate][$rapporteur_t->user_id] >= 3) {
                            continue ;
                        }

                        if ($blockNumber === 2 && $teacherDefenseCount[$currentDate][$rapporteur_t->user_id] >= $maxDefensesPerBlockRapporteur) {
                            $teacherMaxForDay[$currentDate][$rapporteur_t->user_id] = true;
                            continue ;
                        }

                        if (isset($teacherMaxForDay[$currentDate][$rapporteur_t->user_id])) {
                            continue ;
                        }

                        if (isset($rolesAndDefencesTeachers[$rapporteur_user_id])) {
                        if (TeacherDefenses($rolesAndDefencesTeachers[$rapporteur_user_id], 'rapporteur')) {
                            $hourlyAssignedTeachers[] = $rapporteur_user_id;
                            $teacherDefenseCount[$currentDate][$rapporteur_user_id]++;
                          } else { continue;} 
                          } else { continue; }
                        
                    } else {continue ;}
                    $details=getStudentDetails($project);
                    $details2=getStudentDetails2($project);
                    // Add the defense to the schedule
                    $schedule[] = [
                        'date' => $currentDate,
                        'time' => $hour,
                        'classroom' => $block . $classroom,
                        'students' => $details['names'],
                        'encadrant' => "{$encadrant_t->first_name} {$encadrant_t->last_name}",
                        'president' => "{$president_t->first_name} {$president_t->last_name}",
                        'rapporteur' => "{$rapporteur_t->first_name} {$rapporteur_t->last_name}"
                    ];
                    $schedule_teachers[] = [
                        'date' => $currentDate,
                        'time' => $hour,
                        'classroom' => $block . $classroom,
                        'cins' => $details['cins'],
                        'students' => $details['names'],
                        'title' => $details['title'],
                        'emails' => $details['emails'],
                        'specialty' => $details['specialty'],
                        'encadrant' => "{$encadrant_t->first_name} {$encadrant_t->last_name}",
                        'email-encadrant' => "{$encadrant_t->email}",
                        'president' => "{$president_t->first_name} {$president_t->last_name}",
                        'email-president' => "{$president_t->email}",
                        'rapporteur' => "{$rapporteur_t->first_name} {$rapporteur_t->last_name}",
                        'email-rapporteur' => "{$rapporteur_t->email}",
                    ];
                    
                    foreach ($details2 as $studentDetail) {
                        $schedule_admin[] = [
                            'cin' => $studentDetail['cin'], // Handle individual student's CIN
                            'students' => $studentDetail['name'], // Handle individual student's name
                            'status'=>'',
                            'encadrant' => "{$encadrant_t->first_name} {$encadrant_t->last_name}",
                            'president' => "{$president_t->first_name} {$president_t->last_name}",
                            'email-president' => "{$president_t->email}",
                            'rapporteur' => "{$rapporteur_t->first_name} {$rapporteur_t->last_name}",
                            'email-rapporteur' => "{$rapporteur_t->email}",
                        ];
                    }

                    if ($project->partner) {
                        $partnerUserId = Student::where('email', $project->partner)->value('user_id');
                        $projects = $projects->reject(function ($proj) use ($project, $partnerUserId) {
                            return $proj->student_id == $project->student_id || $proj->student_id == $partnerUserId;
                        });
                    } else {
                        $projects->shift();
                    }
                    
                    unset($availableClassrooms[$key]);
                    if (empty($availableClassrooms)) {
                        break;
                    }
            }
        }
    }
     foreach ($schedule_teachers as $schedule_t) {
        Defense::create([
            'date' => $schedule_t['date'],
            'time' => $schedule_t['time'],
            'classroom' => $schedule_t['classroom'],
            'cins' => $schedule_t['cins'],
            'students' => $schedule_t['students'],
            'title' => $schedule_t['title'],
            'emails' => $schedule_t['emails'],
            'specialty' => $schedule_t['specialty'],
            'email_encadrant' => $schedule_t['email-encadrant'],
            'email_president' => $schedule_t['email-president'],
            'email_rapporteur' => $schedule_t['email-rapporteur']
        ]);
    } 
    return [
        'schedule' => $schedule,
        'schedule_teachers' => $schedule_teachers,
        'schedule_admin'=> $schedule_admin,
        'teacherDefenseCount' => $teacherDefenseCount,
        'teacherMaxForDay' => $teacherMaxForDay,
        'rolesAndDefensesTeachers'=>$rolesAndDefencesTeachers
    ];
}

    public function generatePlanning(Request $request)
    {
        $classrooms = $request->input('classrooms');
        $block = $request->input('block');
        $rolesAndDefencesTeachers = $request->input('rolesAndDefencesTeachers'); 
        $schedule = $this->runSchedulingAlgorithm($classrooms, $block, $rolesAndDefencesTeachers);
        return response()->json(['schedule' => $schedule]);   
    }
    public function generateFile(Request $request)
{
    $schedule = $request->input('schedule'); // Retrieve schedule from request
    $type = $request->input('type');

    if ($type === 'student') {
        $scheduleStudents = $schedule['schedule'];
        $pdf = PDF::loadView('planningStudents', compact('scheduleStudents'))->setPaper('a4', 'landscape');
        return $pdf->download('schedule_students.pdf');  
    } elseif ($type === 'teacher') {
        $scheduleTeachers = $schedule['schedule_teachers'];
        $pdf = PDF::loadView('planningTeachers', compact('scheduleTeachers'))->setPaper('a4', 'landscape');
        return $pdf->download('schedule_teachers.pdf');
    } elseif ($type === 'admin') {
        $scheduleAdmin = $schedule['schedule_admin'];
        return Excel::download(new AdminScheduleExport($scheduleAdmin), 'schedule_admin.xlsx');
    }

    return response()->json(['error' => 'Invalid type provided'], 400);
}
public function deleteAll()
{
    try {
        Defense::deleteDefenses(); // Call the model's static method
        return response()->json(['message' => 'All defenses deleted successfully.'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to delete defenses.', 'details' => $e->getMessage()], 500);
    }
}
}


