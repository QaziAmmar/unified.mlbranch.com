<?php

namespace App\Http\Controllers;

use App\Models\EmailList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailListController extends Controller
{
    //


    public function email_list(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error: Incorrect or missing parameters',
                'status' => false,
                'data' => $validator->errors()
            ], 401);
        }

        $user = [ 'email' => request('email')];
        $user = EmailList::create($user);

        if ($user != null) {
            return response()->json([
                'status' => true,
            ], 200);
        }

        
    }


    public function create(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(), [
            'email_list' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error: Incorrect or missing parameters',
                'status' => false,
                'data' => $validator->errors()
            ], 401);
        }

        $email_list = request('email_list');
        
        foreach ($email_list as $email) {
            # code...
            $insert_email['email'] = $email;
            $email_list = EmailList::create($insert_email);
        }

        if ($email_list != null) {
            return response()->json([
                'message' => 'Email Entered successfully',
                'status' => true,
            ], 200);
        }

        
    }


}
