<?php

use App\Http\Controllers\Api\admin\EmployeeAndAttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

Route::post('emp/login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);

    Route::middleware('role:1')->prefix('admin')->group(function () {
        //Employees
        Route::get('allEmps', [EmployeeAndAttendanceController::class, 'getEmpDetails']);
        Route::post('emp/register', [EmployeeAndAttendanceController::class, 'empRegister']);
        Route::get('emp/{id}', [EmployeeAndAttendanceController::class, 'getByIdEmpDetails']);
        Route::put('emp/update/{id}', [EmployeeAndAttendanceController::class, 'updateEmpDetails']);
        Route::delete('emp/delete/{id}', [EmployeeAndAttendanceController::class, 'destroyEmpDetails']);

        Route::get('allEmpAttendance', [EmployeeAndAttendanceController::class, 'getEmpAttendanceByDate']);
    });

    Route::middleware('role:2')->prefix('emp')->group(function () {
        Route::post('checkin', [AttendanceController::class, 'empcheckin']);
        Route::post('checkout', [AttendanceController::class, 'empcheckout']);
    });
});