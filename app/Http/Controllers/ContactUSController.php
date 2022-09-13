<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactUSController extends Controller
{
    //
    public function contact_us()
    {
        # code...

        $validator = Validator::make(request()->all(), [
            
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'email|required',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error: Incorrect or missing parameters',
                'status' => false,
                'data' => $validator->errors()
            ], 401);
        }

        $contact_us = request(['first_name', 'last_name', 'email', 'message']);
        $contact_us = ContactUs::create($contact_us);

        if ($contact_us != null) {
            return response()->json([
                'status' => true,
                'message' => 'Email sent successfully'
            ], 200);
        }
        
    }

}
