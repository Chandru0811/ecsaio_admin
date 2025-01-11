<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmployeeAndAttendanceController extends Controller
{
    use ApiResponses;

    public function getEmpDetails()
    {
        $employees = User::where('role', 2)->get();

        return $this->success('Employees Retrieved Successfully!', $employees);
    }

    public function empRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string',
            'emp_id'    => 'required|string',
            'email'     => 'required|email|unique:users,email',
            'join_date' => 'required|date',
            'password'  => 'required|string|min:8|confirmed'
        ], [
            'name.required'      => 'The name field is required.',
            'emp_id.required'    => 'The emp id field is required.',
            'email.required'     => 'The email field is required.',
            'email.email'        => 'The email format is invalid.',
            'email.unique'       => 'The email is already in use.',
            'join_date.required' => 'The join date field is required.',
            'join_date.date'     => 'The join date must be a valid date format.',
            'password.required'  => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $emp = User::create([
            'name' => $request->name,
            'emp_id' => $request->emp_id,
            'email' => $request->email,
            'join_date' => $request->join_date,
            'password' => bcrypt($request->password),
            'role' => 2
        ]);

        $token = $emp->createToken('Personal Access Token')->accessToken;
        $success['token'] = $token;
        $success['userDetails'] =  $emp;

        return $this->success('Employee Registered Successfully!', $success);
    }

    public function getByIdEmpDetails($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->error('Employee Details Not Found.', ['error' => 'Employee Details Not Found.']);
        }
        
        return $this->success('Employee Details Retrived Succesfully!', $user);
    }

    public function updateEmpDetails(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->error('Employee Details Not Found.', ['error' => 'Employee Details Not Found.']);
        }

        $validator = Validator::make($request->all(), [
            'name'      => 'required|string',
            'emp_id'    => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'join_date' => 'required|date',
            'password'  => 'required|string|min:8|confirmed'
        ], [
            'name.required'      => 'The name field is required.',
            'emp_id.required'    => 'The emp id field is required.',
            'email.required'     => 'The email field is required.',
            'email.email'        => 'The email format is invalid.',
            'email.unique'       => 'The email is already in use.',
            'join_date.required' => 'The join date field is required.',
            'join_date.date'     => 'The join date must be a valid date format.',
            'password.required'  => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->all());

        return $this->success('Employee Details Updated Successfully!', $user);
    }

    public function destroyEmpDetails($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->error('Employee Details Not Found.', ['error' => 'Employee Details Not Found.']);
        }

        $user->delete();

        return $this->ok('Employee Details Deleted Successfully!');
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
