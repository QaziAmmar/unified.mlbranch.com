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
                'message' => 'Email Added successfully',
                'status' => true,
                'data' => (object)[]
            ], 401);
        }

        
    }

}
