<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class EditProfileController extends Controller
{



    public function bio(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'bio' => 'required|string|'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error: Incorrect or missing parameters',
                'status' => false,
                'data' => $validator->errors()
            ], 401);
        }

        $user = User::find(request('user_id'));

        if ($user != null) {

            // update the user password in database.
            $user->bio = request('bio');
            $user->save();

            $data = [
                'message' => 'User bio updated successfully',
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

    public function skill(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'skills' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error: Incorrect or missing parameters',
                'status' => false,
                'data' => $validator->errors()
            ], 401);
        }

        $user = User::find(request('user_id'));

        $skills = $user->skills;
        $new_skills = request('skills');


        if ($user != null) {

            // update the user password in database.
            array_merge($skills, $new_skills);
            $user->skills = $new_skills;
            $user->save();

            $data = [
                'message' => 'User skill added',
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


    public function skill_delete(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'skill' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error: Incorrect or missing parameters',
                'status' => false,
                'data' => $validator->errors()
            ], 401);
        }

        $user = User::find(request('user_id'));

        // if no user found in database
        if ($user == null ) {

            $data = [
                'message' => 'User not found',
                'status' => false,
                'data' => (object)[]
            ];

            return response()->json($data, 401);
        }

        // update the user password in database.
        $skills = $user->skills;
        $skill_for_delete = request('skill');
        $index_for_delete = array_search($skill_for_delete, $skills);

        
        // if no skill in the database.
        if ($index_for_delete === false) {
            $data = [
                'message' => 'Skill not found',
                'status' => false,
                'data' => (object)[]
            ];

            return response()->json($data, 401);
        }

        unset($skills[$index_for_delete]);
        $skills = array_values($skills);

        $user->skills = $skills;
        $user->save();

        $data = [
            'message' => 'User skill deleted',
            'status' => true,
            'data' => (object)[]
        ];

        return response()->json($data, 200);      
    }


    public function interest(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'interstes' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error: Incorrect or missing parameters',
                'status' => false,
                'data' => $validator->errors()
            ], 401);
        }

        $user = User::find(request('user_id'));
        // finding previouse interses
        $interstes = $user->interstes;
        $new_interstes = request('interstes');
        

        if ($user != null) {

            // update the user password in database.
            array_merge($interstes, $new_interstes);
            $user->interstes = $new_interstes;
            // update the user password in database.
            $user->save();



            $data = [
                'message' => 'User interest added',
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


    public function interest_delete(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'interstes' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error: Incorrect or missing parameters',
                'status' => false,
                'data' => $validator->errors()
            ], 401);
        }

        $user = User::find(request('user_id'));

        // if no user found in database
        if ($user == null ) {

            $data = [
                'message' => 'User not found',
                'status' => false,
                'data' => (object)[]
            ];

            return response()->json($data, 401);
        }

        // update the user password in database.
        $interstes = $user->interstes;
        $interst_for_delete = request('interstes');
        $index_for_delete = array_search($interst_for_delete, $interstes);

        
        // if no skill in the database.
        if ($index_for_delete === false) {
            $data = [
                'message' => 'Interst not found',
                'status' => false,
                'data' => (object)[]
            ];

            return response()->json($data, 401);
        }

        unset($interstes[$index_for_delete]);
        $interstes = array_values($interstes);

        $user->interstes = $interstes;
        $user->save();

        $data = [
            'message' => 'User interst deleted',
            'status' => true,
            'data' => (object)[]
        ];

        return response()->json($data, 200);      
    }
}
