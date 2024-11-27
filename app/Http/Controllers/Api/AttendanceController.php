<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function empcheckin(Request $request)
    {
        $user = auth()->user();
        $currentDate = Carbon::now('Asia/Kolkata')->toDateString();
        $currentTime = Carbon::now('Asia/Kolkata')->toTimeString();

        $existingAttendance = Attendance::where('user_id', $user->id)
                                        ->where('date', $currentDate)
                                        ->first();

        if ($existingAttendance) {
            return response()->json(['message' => 'Already checked in for today'], 400);
        }

        Attendance::create([
            'user_id' => $user->id,
            'date' => $currentDate,
            'checkin' => $currentTime,
        ]);

        return response()->json(['message' => 'Check-in time recorded successfully'], 200);
    }

    public function empcheckout(Request $request)
    {
        $user = auth()->user();
        $currentTime = Carbon::now('Asia/Kolkata')->toTimeString();
        $currentDate = Carbon::now('Asia/Kolkata')->toDateString();
        
        $existingAttendance = Attendance::where('user_id', $user->id)
                                        ->where('date', $currentDate)
                                        ->where('checkout','!=',null)
                                        ->first();
        
        if ($existingAttendance) {
            return response()->json(['message' => 'Already checkOut for today'], 400);
        }

        $attendance = Attendance::where('user_id', $user->id)
                            ->where('date', Carbon::now()->toDateString())
                            ->first();

        if ($attendance) {
            $attendance->update([
                'checkout' => $currentTime, 
            ]);
                        
            return response()->json(['message' => 'Checkout time recorded successfully'],200);
        } else {
            return response()->json(['error' => 'No check-in record found for today'], 404);
        }
    
        

    }
}
