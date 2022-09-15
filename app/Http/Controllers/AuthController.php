<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Education;
use App\Models\Institute;
use App\Models\Subscription;
use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\User_profile_images;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Store a newly created resource in storage.
      Note: whenever a user is created a subscription entry is automatically created for that user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique:users|email',
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
                'message' => 'Unable to create the user',
                'status' => false,
                'data' => $validator->errors()
            ], 401);
        }

        // creating new user.
        $request['password'] = Hash::make($request->password);
        $user = request(['name', 'email', 'password', 'age', 'role', 'looking_for', 'gender', 'firebase_id']);

        $user = User::create($user);

        /**
         As new user is created make a entry for its subscription
         */
        Subscription::create(['user_id' => $user->id]);

        // institute details
        $institute = Institute::create([
            'name' => request('institute_name')
        ]);

        // education details
        $edu = request(['course', 'city']);
        $edu['user_id'] = $user->id;
        $edu['institute_id'] = $institute->id;
        $edu = Education::create($edu);

        // make a self entry in suggestion table.
        Suggestion::create([
            'user_id' => $user->id,
            'friend_id' => $user->id,
            'status' => config('global.rejected')
        ]);

        $data = [
            'message' => 'User created successfully',
            'status' => true,
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
        if (!Auth::attempt($credentials)) {

            $data = [
                'message' => 'Email or Password is in correct',
                'status' => false,
                'data' => (object)[]
            ];

            return response()->json($data, 401);
        }


        // fetch the user and return it back.


        $user = Auth::user();
        array_walk_recursive($user, function (&$item, $key) {
            $item = null === $item ? '' : $item;
        });

        // append education with user
        $user['education'] = Education::where('user_id', $user->id)->first();
        $user['sub_pictures'] = User_profile_images::where('user_id', $user->id)->get();
        $user['business_created'] = Business::where('user_id', $user->id)->first() == null ? 0 : 1;

        $data = [
            'message' => 'Login successful',
            'status' => true,
            'data' => [
                'user' => $user
            ]
        ];
        return response()->json($data, 200);
    }
}
