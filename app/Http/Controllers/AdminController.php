<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    //

    public function dashboard(){
        $data = [
            'users'=>User::count(),
            'active'=>User::where('status',1)->count(),
            'inactive'=>User::where('status',0)->count()
        ];

        return response()->json([
            'success' => true,
            'message' => 'Fetch admin dashboard data',
            'data' => $data
        ]);
    }
}
