<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Institute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'age' => 'required',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'looking_for' => 'required|string',
            'gender' => 'required|string',
            'institute_name' => 'required|string',
            'course' => 'required|string',
            'city' => 'required|string',
            'firebase_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Unauthorize', 
                'data' => $validator->errors()
            ], 200);
        }

        // creating new user.
        $request['password'] = Hash::make($request->password);
        $user = request(['name', 'email','password', 'age', 'role', 'looking_for', 'gender', 'firebase_id']);

        $user = User::create($user);
        
        // institute details
        $institute = Institute::create([
            'name' => request('institute_name')
        ]);

        // education details
        $edu = request(['course', 'city']);
        $edu['user_id'] = $user->id;
        $edu['institute_id'] = $institute->id; 
        $edu = Education::create($edu);
        
        $data = [
            'message' => 'User created successfully', 
            'data' => [
                'user' => $user
            ]
        ];

        return response($data, 200);

    }

    public function login(Request $request)
    {

        # code...
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Unauthorize', 
                'data' => $validator->errors()
            ], 200);
        }

        // fetching 
        $credentials = request(['email', 'password']);
        // check user in database
        if (!Auth::attempt($credentials)){

            $data = [
                'message' => 'Email or Password is in correct', 
                'data' => (object)[]
            ];

             return response()->json($data, 401);
        }

        // fetch the user and return it back.
        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => Auth::user()
            ]
        ]);

    } 
}
