<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function adminTest()
    {
        return response()->json([
            'message' => 'Admin access granted!',
            'user' => Auth::user()->email,
            'timestamp' => now()
        ]);
    }

    public function customerTest()
    {
        return response()->json([
            'message' => 'Customer access granted!',
            'user' => Auth::user()->email,
            'timestamp' => now()
        ]);
    }

    public function roleTest()
    {
        return response()->json([
            'message' => 'Role-based access granted!',
            'user' => Auth::user()->email,
            'roles' => 'admin,manager', // The roles required for this route
            'timestamp' => now()
        ]);
    }

    public function apiTest(Request $request)
    {
        return response()->json([
            'message' => 'API access granted!',
            'token' => $request->bearerToken(),
            'timestamp' => now()
        ]);
    }

    public function guestTest()
    {
        return response()->json([
            'message' => 'This should only be accessible to guests',
            'authenticated' => Auth::check(),
            'timestamp' => now()
        ]);
    }
}
