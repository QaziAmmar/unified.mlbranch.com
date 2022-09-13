<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //


     /**
     * This function will gather the data from different tables
     * and show them in user's shop tab.
     * Need to @return 3 thing.
      1. Banner Image
      2. Feature Products 
      3. Recent products
     */

    public function users()
    {
        # code...

        $user_id = request('user_id');
        
        $users =  User::inRandomOrder()
        ->with("post_psbs")
        ->limit(20)
        ->get();

        $data = [
            'status' => true,
            'message' => "20 users are send to mobile device",
            'users' => $users

        ];

        return response()->json($data, 200);

    }


    public function friend_request()
    {
        # code...

        $user_id = request('user_id');
        $friend_id = request('friend_id');
        $status = request('status');
  

    }

    public function validationError($validator)
    {
        # code...
        return response()->json([
            'message' => 'Error: Incorrect or missing parameters',
            'status' => false,
            'data' => $validator->errors()
        ], 401);
    }

    public function general_error_with($message)
    {
        # code...
        return response()->json([
            'message' => $message,
            'status' => false,
            'data' => (object)[]
        ], 401);
    }


}
