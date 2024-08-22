<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class studentController extends Controller
{
    public function checkCinExists(Request $request)
{
    $cinExists = Student::where('cin', $request->cin)->exists();
    return response()->json(['exists' => $cinExists]);
}

}
