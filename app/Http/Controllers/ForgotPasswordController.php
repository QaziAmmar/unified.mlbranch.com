<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                'message' => 'Unable to verify OTP',
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

   
}
