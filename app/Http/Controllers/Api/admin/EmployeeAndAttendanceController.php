<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;

class EmployeeAndAttendanceController extends Controller
{
    use ApiResponses;

    public function getEmpDetails()
    {
        $employees = User::where('role', 2)->get();

        return $this->success('Employees Retrieved Successfully!', $employees);
    }

    public function getEmpAttendanceByDate(Request $request)
    {
        $date = $request->input('date');

        $attendanceRecords = Attendance::where('date', $date)
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->get(['users.emp_id', 'users.name', 'users.email', 'attendances.checkin', 'attendances.checkout']);

        return $this->success('Employees Attendance Retrieved Successfully!', $attendanceRecords);
    }
}
