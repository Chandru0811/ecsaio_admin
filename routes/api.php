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
        Route::get('allEmps', [EmployeeAndAttendanceController::class, 'getEmpDetails']);
        Route::post('allEmpAttendance', [EmployeeAndAttendanceController::class, 'getEmpAttendanceByDate']);
    });

    Route::middleware('role:2')->prefix('emp')->group(function () {
        Route::post('checkin', [AttendanceController::class, 'empcheckin']);
        Route::post('checkout', [AttendanceController::class, 'empcheckout']);
    });
});