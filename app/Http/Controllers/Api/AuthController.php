<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponses;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emp_id' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if (Auth::attempt(['emp_id' => $request->emp_id, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('Personal Access Token')->accessToken;
            $success['token'] = $token;
            $success['userDetails'] =  $user;

            return response()->json([
                'success' => true,
                'status' => 200,
                'data' => $success,
                'message' => 'LoggedIn Successfully!'
            ], 200);

            return $this->success('LoggedIn Successfully!', $success);
        }

        return response()->json(['success' => false,
            'status' => 400,
            'data' => [],
            'message' => 'Invalid email or password'],422);

    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'logged Out Successfully!'
        ], 200);
    }
}
