<?php

namespace App\Http\Controllers\SubAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $instituteId = Auth::user()->institute_id;
        $search      = trim($request->query('search', ''));
        $batchFilter = $request->query('batch_id', '');

        $batches = DB::table('batches')
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $query = DB::table('students')
            ->where('students.institute_id', $instituteId)
            ->join('batches', 'students.batch_id', '=', 'batches.id')
            ->leftJoin('courses', 'batches.course_id', '=', 'courses.id')
            ->select(
                'students.id',
                'students.roll_number',
                'students.name',
                'students.email',
                'students.phone',
                'students.medium',
                'students.is_active',
                'batches.id as batch_id',
                'batches.name as batch_name',
                'batches.code as batch_code',
                'courses.name as course_name'
            )
            ->orderBy('students.name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('students.name', 'like', "%{$search}%")
                  ->orWhere('students.roll_number', 'like', "%{$search}%")
                  ->orWhere('students.email', 'like', "%{$search}%");
            });
        }

        if ($batchFilter !== '') {
            $query->where('students.batch_id', $batchFilter);
        }

        $students = $query->paginate(30)->withQueryString();

        $total  = DB::table('students')->where('institute_id', $instituteId)->count();
        $active = DB::table('students')->where('institute_id', $instituteId)->where('is_active', true)->count();

        return view('sub-admin.students.index', compact('students', 'batches', 'search', 'batchFilter', 'total', 'active'));
    }
}
