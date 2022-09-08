<?php

namespace App\Http\Controllers;

use App\Models\PSB;
use App\Models\PSBImages;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PSBController extends Controller
{
    //
    public function create(Request $request)
    {
        //
        # code...
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'title' => 'required|string|',
            'description' => 'required|string|',
            'psb_images' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $psb = request(['user_id', 'title', 'description']);

        // first create the product and then get the product id
        $psb = PSB::create($psb);
        if ($psb == null) {
            return $this->general_error_with("psb creation fail");
        }

        $psb['psb_images'] = $this->save_psb_images(request('psb_images'), $psb->id);


        return response()->json([
            'message' => 'PSB added successfully',
            'status' => true,
            'data' => $psb
        ], 200);
    }

    public function detail()
    {
        # code...

        $psb_id = request('psb_id');

        $psb = PSB::where('id', $psb_id)->with("psb_images")->first();
        if ($psb == null) {
            return $this->general_error_with("No psb found");
        }

        return response()->json([
            'message' => 'PSB data found successfully',
            'status' => true,
            'data' => $psb
        ], 200);
    }

    public function all_psbs()
    {
        # code...
        $user_id = request('user_id');
        $psb = PSB::where('user_id', $user_id)->with("psb_images")->get();
        if (count($psb) == 0) {
            return $this->general_error_with("No psb found");
        }

        return response()->json([
            'message' => 'PSB data found successfully',
            'status' => true,
            'data' => $psb
        ], 200);
    }


    public function save_psb_images($psb_images, $psb_id)
    {

        if ($psb_images == null) {
            return [];
        }
        $save_psb_images = [];
        foreach ($psb_images as $image) {


            $image_link = $this->save_base64_image($image);

            $data = [
                'psb_id' => $psb_id,
                'image_link' => $image_link
            ];
            $psb_img = PSBImages::create($data);

            array_push($save_psb_images, $psb_img);
        }
        return $save_psb_images;
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

    public function general_error_with($message)
    {
        # code...
        return response()->json([
            'message' => $message,
            'status' => false,
            'data' => (object)[]
        ], 401);
    }


    public function save_base64_image($base64File)
    {
        # code...
        $fileData = base64_decode($base64File);

        $name = 'psb/' . Str::random(15) . '.png';

        Storage::put('public/' . $name, $fileData);
        // update the user's profile_pic
        return $name;
    }
}
