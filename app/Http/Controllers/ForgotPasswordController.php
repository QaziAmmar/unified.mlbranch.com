<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function generate_otp(Request $request)
    {

        # code...
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Unable to send the OTP',
                'data' => $validator->errors()
            ], 200);
        }

        // fetching email from user
        $email = request(['email']);

        // generating random number
        $digits = 4;
        $otp_code = rand(pow(10, $digits - 1), pow(10, $digits) - 1);

        $status = User::where('email', $email)->update(['email_code' => $otp_code]);

        if ($status) {

            $data = [
                'message' => 'OTP is sent Successfully',
                'status' => true,
                'data' => [
                    'otp' => $otp_code
                ]
            ];

            return response()->json($data, 200);
        } else {

            $data = [
                'message' => 'Email is incorrect',
                'status' => false,
                'data' => (object)[]
            ];

            return response()->json($data, 401);
        }
    }

    public function verify_otp(Request $request)
    {

        # code...
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'otp' => 'required|string|min:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error: incorrect or missing parameters',
                'status' => false,
                'data' => $validator->errors()
            ], 401);
        }


        // fetching email from user
        $user = DB::table('users')->where('email', request('email'))
            ->where('email_code', request('otp'))->first();



        if ($user != null) {

            // this function will remove the null values form the response.
            array_walk_recursive($user, function (&$item, $key) {
                $item = null === $item ? '' : $item;
            });

            $data = [
                'message' => 'OTP verified',
                'status' => true,
                'data' => [
                    'user' => $user
                ]
            ];

            return response()->json($data, 200);
        } else {

            $data = [
                'message' => 'Incorrect OTP ',
                'status' => false,
                'data' => (object)[]
            ];

            return response()->json($data, 401);
        }
    }

    public function create_password(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Input Field Error',
                'status' => false,
                'data' => $validator->errors()
            ], 401);
        }

        $user = User::find(request('user_id'));
        

        if($user != null) {
            
            // update the user password in database.
            $user->password = Hash::make($request->password);
            $user->save();

            $data = [
                'message' => 'Password updated successfully',
                'status' => true,
                'data' => (object)[]
            ];

            return response()->json($data, 200);

        } else {
            $data = [
                'message' => 'User not found',
                'status' => false,
                'data' => (object)[]
            ];

            return response()->json($data, 401);
        }
    }
   
}
