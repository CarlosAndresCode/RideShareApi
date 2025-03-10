<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedNotification;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validate the phone number
         $request->validate([
            'phone' => 'required|numeric|digits:10'
        ]);

        // Check if the phone number is in the database
        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);

        if (!$user) {
            return response()->json([
                'message' => 'Could not find phone number'
            ]);
        }

        $user->notify(new LoginNeedNotification());

        return response()->json([
            'message' => 'Login code sent'
        ]);
    }

    public function verify(Request $request)
    {
        // Validate the phone number and login code
        $request->validate([
            'phone' => 'required|numeric|digits:10',
            'login_code' => 'required|numeric|digits:6'
        ]);

        // Check if the phone number is in the database
        $user = User::where('phone', $request->phone)
            ->where('login_code', $request->login_code)
            ->first();

        if ($user) {
            $user->update([
                'login_code' => null
            ]);
            return $user->createToken($request->login_code)->plainTextToken;
        }

        return response()->json([
            'message' => 'Invalid login code'
        ], 401);
    }
}
