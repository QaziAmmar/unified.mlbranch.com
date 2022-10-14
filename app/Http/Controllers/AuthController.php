<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Education;
use App\Mail\GenerateOTPMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Subscription;
use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\User_profile_images;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            'institute_id' => 'required|string',
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
        $user = request(['name', 'email', 'password', 'age', 'role', 'looking_for', 'gender', 'firebase_id', 'institute_id']);

        $user = User::create($user);

        /**
         As new user is created make a entry for its subscription
         */
        Subscription::create(['user_id' => $user->id]);

        // education details
        $edu = request(['course', 'city']);
        $edu['user_id'] = $user->id;
        $edu['institute_id'] = request('institute_id');
        $edu = Education::create($edu);

        // make a self entry in suggestion table so that person did't get his own suggestion.
        Suggestion::create([
            'user_id' => $user->id,
            'friend_id' => $user->id,
            'status' => config('global.rejected')
        ]);

        // adding data to make model same like login
        $user['bio'] = '';
        $user['lat'] = '';
        $user['long'] = '';
        
        $user['g_token'] = '';
        $user['fb_token'] = '';
        $user['apl_token'] = '';
        $user['email_code'] = '';
        
        $user['device_id'] = '';
        $user['profile_pic'] = '';
        $user['education'] = $edu;
        $user['sub_pictures'] = [];
        $user['business_created'] = 0;
        $user['interstes'] = [];
        $user['skills'] = [];

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


    public function verify_account(Request $request)
    {

        # code...
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'user_id' => 'required|string',
            'document_img' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Unable to send the OTP',
                'data' => $validator->errors()
            ], 200);
        }

        // fetching email from user
        $user_id = request('user_id');
        $email = request('email');
        
        // Save document picture on server by the id for user.

         // decode the base64 image
         $base64File = request('document_img');
         $fileData = base64_decode($base64File);
         $name = 'users_doc/' . $user_id . '.png';
         Storage::put('public/' . $name, $fileData);
         

        // generating random number
        $digits = 4;
        $otp_code = rand(pow(10, $digits - 1), pow(10, $digits) - 1);

        $status = User::where('id', $user_id)->update(['email_code' => $otp_code]);
        
        // check either user exist in the databse of not.
        if ($status) {

            // send email to this Email Address
            // adding some commet 
            $main_data = ['message' => 'Unified Account Verification Code '. $otp_code ];
            Mail::to($email)->send(new GenerateOTPMail($main_data));

            $data = [
                'message' => 'You account will be verified by admin soon',
                'status' => true,
                'data' => [
                    'otp' => $otp_code
                ]
            ];

            return response()->json($data, 200);
        } else {

            $data = [
                'message' => 'No User id is Founeded in Database',
                'status' => false,
                'data' => (object)[]
            ];

            return response()->json($data, 401);
        }
    }

    // public function verify_account_otp(Request $request)
    // {

    //     # code...
    //     $validator = Validator::make($request->all(), [
    //         'user_id' => 'required|string',
    //         'otp' => 'required|string|min:4'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'message' => 'Error: incorrect or missing parameters',
    //             'status' => false,
    //             'data' => $validator->errors()
    //         ], 401);
    //     }


    //     // fetching email from user
    //     $user = User::where('id', request('user_id'))
    //     ->where('email_code', request('otp'))->first();


    //     if ($user != null) {

    //         // this function will remove the null values form the response.
            
    //         $user->verified = 1;
    //         $user->email_code = 0;
    //         $user->save();
    //         $data = [
    //             'message' => 'Accounct verified',
    //             'status' => true,
    //             'data' => (object)[]
    //         ];

    //         return response()->json($data, 200);
    //     } else {

    //         $data = [
    //             'message' => 'Incorrect OTP ',
    //             'status' => false,
    //             'data' => (object)[]
    //         ];

    //         return response()->json($data, 401);
    //     }
    // }
}
