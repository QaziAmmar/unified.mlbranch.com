<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Institute;
use App\Models\User;
use App\Models\User_profile_images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


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
            'skills' => 'required'
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
            array_push($skills, $new_skills);
            $user->skills = $skills;
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
        if ($user == null) {

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
        // finding previouse interses
        $interstes = $user->interstes;
        $new_interstes = request('interstes');

        if ($user != null) {

            // update the user password in database.

            array_push($interstes, $new_interstes);
            $user->interstes = $interstes;
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
        if ($user == null) {

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

    public function edit_profile_image(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'profile_pic' => 'required'
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
        if ($user == null) {

            $data = [
                'message' => 'User not found',
                'status' => false,
                'data' => (object)[]
            ];

            return response()->json($data, 401);
        }
        // decode the base64 image
        $base64File = request('profile_pic');
        $fileData = base64_decode($base64File);

        $name = 'users_profile/' . Str::random(15) . '.png';

        Storage::put('public/' . $name, $fileData);
        // update the user's profile_pic
        $user->profile_pic = $name;
        $user->save();
        //return a response as json assuming you are building a restful API 
        return response()->json([
            'message' => 'Profile picture updated',
            'status' => true,
            'data' => [
                'profile_pic' => $name
            ]
        ], 200);
    }

    // add profile multiple images

    public function add_profile_sub_images(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'sub_images' => 'required|array'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user = User::find(request('user_id'));
        // if no user found in database
        if ($user == null) {

            $data = [
                'message' => 'User not found',
                'status' => false,
                'data' => (object)[]
            ];

            return response()->json($data, 401);
        }


        // decode the base64 image
        $profile_images = [];
        $base64Files = request('sub_images');
        foreach ($base64Files as $base64pic) {
            # code...
            $fileData = base64_decode($base64pic);
            $name = 'users_sub_profile/' . Str::random(15) . '.png';
            Storage::put('public/' . $name, $fileData);

            $profile_image = [
                "user_id" => $user->id,
                "picture" => $name
            ];

            $profile_image = User_profile_images::create($profile_image);

            array_push($profile_images, $profile_image);
        }

        // append URL with each profile Image
        //return a response as json assuming you are building a restful API 
        return response()->json([
            'message' => 'Profile sub pictures updated',
            'status' => true,
            'data' => [
                'profile_pic' => $profile_images
            ]
        ], 200);
    }

    public function delete_profile_sub_images(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(), [
            'image_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        User_profile_images::where('id', request('image_id'))->delete();

        // append URL with each profile Image
        //return a response as json assuming you are building a restful API 
        return response()->json([
            'message' => 'Profile sub picture deleted',
            'status' => true,
            'data' => (object)[]
        ], 200);
    }
    // add user education.
    public function add_education(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'institute_id' => 'required',
            'course' => 'required|',
            'city' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $user = User::find(request('user_id'));
        // if no user found in database
        if ($user == null) {
            return $this->showError("User Not found");
        }

        $institute = Institute::find(request('institute_id'));
        if ($institute == null) {
            return  $this->showError("Institute not found");
        }

        // education details
        $edu = Education::where('user_id', $user->id)->first();

        if ($edu == null) {
            // create new record in database
            $edu = request(['institute_id', 'course', 'city']);
            $edu['user_id'] = $user->id;
            $edu = Education::create($edu);
            $message = 'Education added successfully';
        } else {
            // update the existing record.
            $edu->institute_id = request('institute_id');
            $edu->course = request('course');
            $edu->city = request('city');
            $edu->save();
            $message = 'Education updated successfully';
            
        }

        $data = [
            'message' => $message,
            'status' => true,
            'data' => $edu
        ];

        return response($data, 200);
    }

    public function delete_education(Request $request)
    {
        # code...

        $validator = Validator::make($request->all(), [
            'education_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }


        // education details
        $edu_id = request('education_id');

        $edu = Education::where('id', $edu_id)->delete();

        $data = [
            'message' => 'Education deleted successfully',
            'status' => true,
            'data' => (object)[]
        ];

        return response($data, 200);
    }


    public function test()
    {

        # code...

        $user = User::find("1");
        echo $user->profile_pic;
        // dd($user);


    }


    public function showError($message)
    {
        # code...
        $data = [
            'message' => $message,
            'status' => false,
            'data' => (object)[]
        ];

        return response()->json($data, 401);
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
}
