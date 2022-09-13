<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\BusinessExternalLinks;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        # code...
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required|string|',
            'location_name' => 'required|string|',
            'lat' => 'required|string|',
            'long' => 'required|string|',
            'description' => 'required|string|',
            'bannar_img' => 'required|string|',
            'business_img' => 'required|string|',
            'external_links' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        if ($this->isBusinessRegisterd(request('user_id'))) {
            return $this->general_error_with("Business is already register for this user");
        }

        $subscription = Subscription::where('user_id', request('user_id'))->first();
        if ($subscription == null) {
            return $this->general_error_with("No user found against this ID");
        }
        if ($subscription->create_business == '') {
            return $this->general_error_with("Please Subscribe before creating business");
        }

        $business = request(['user_id', 'name', 'location_name', 'lat', 'long', 'description']);
        // convert the image into base 64 and save it into the folder.
        $business['bannar_img'] = $this->save_base64_image(request('bannar_img'));
        $business['business_img'] = $this->save_base64_image(request('business_img'));

        $business = Business::create($business);
        $business['external_links'] = $this->save_external_link(request('external_links'), $business->id);

        return response()->json([
            'message' => 'Business Created successfully',
            'status' => true,
            'data' => $business
        ], 200);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function show($user_id)
    {
        //
        if ($user_id == null) {
            return $this->general_error_with("user_id is missing");
        }

        // check if you have business subscription then you can create your business

        $business = Business::where('user_id', $user_id)
        ->with("post_products")
        ->first();

        if ($business == null) {
            return $this->general_error_with("Business not found");
        }

        if ($business == null) {
            return $this->general_error_with("Business not found against this user ID");
        } else {
            
            $business['external_links'] = BusinessExternalLinks::where('business_id', $business->id)->get();
            $business['firebase_id'] = User::where('id', $user_id)->pluck('firebase_id')->first();


            return response()->json([
                'message' => 'Business found successfully',
                'status' => true,
                'data' => $business
            ], 200);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBusinessRequest  $request
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        //
        # code...
        $validator = Validator::make($request->all(), [
            'business_id' => 'required',
            'name' => '|string',
            'location_name' => '|string',
            'lat' => '|string',
            'long' => 'string|',
            'description' => 'string|',
            'bannar_img' => 'string|',
            'business_img' => 'string|'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $business = Business::find(request('business_id'));
        // convert the image into base 64 and save it into the folder.
        $date = request(['business_id']);
        // check which fields are set for update then only update those fields.
        $date = $this->generate_date_for_update($date, $business);
        // update the business
        $business->update($date);

        return response()->json([
            'message' => 'Business updated successfully',
            'status' => true,
            'data' => $business
        ], 200);
    }


    public function generate_date_for_update($date, $business)
    {
        # code...
        if (request('name') != null) {
            $date['name'] = request('name');
        }
        if (request('location_name') != null) {
            $date['location_name'] = request('location_name');
        }
        if (request('lat') != null) {
            $date['lat'] = request('lat');
        }
        if (request('long') != null) {
            $date['long'] = request('long');
        }
        if (request('description') != null) {
            $date['description'] = request('description');
        }
        if (request('bannar_img') != null) {
            $date['bannar_img'] = $this->save_base64_image(request('bannar_img'));
        }
        if (request('business_img') != null) {
            $date['business_img'] = $this->save_base64_image(request('business_img'));
        }

        return $date;
    }

    /**
     * Add the link first time when user is creating his business
     *
     * @param  \App\Http\Requests\UpdateBusinessRequest  $request
     */
    public function save_external_link($external_links, $business_id)
    {

        if ($external_links == null) {
            return [];
        }
        $save_external_links = [];

        foreach ($external_links as $link) {

            $link['business_id'] = $business_id;
            $external_link = BusinessExternalLinks::create($link);

            array_push($save_external_links, $external_link);
        }
        return $save_external_links;
    }

    /**
     * Add the link from the edit business profile
     *
     * @param  \App\Http\Requests\UpdateBusinessRequest  $request
     */
    public function add_external_link(Request $request)
    {
        //
        # code...
        $validator = Validator::make($request->all(), [
            'business_id' => 'required',
            'external_link' => 'required|string|',
            'category' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $external_link = request(['business_id', 'external_link', 'category']);

        // first create the product and then get the product id
        $external_link = BusinessExternalLinks::create($external_link);
        if ($external_link == null) {
            return $this->general_error_with("fail to add external link");
        }

        return response()->json([
            'message' => 'External Link added successfully',
            'status' => true,
            'data' => $external_link
        ], 200);
    }

    /**
     * Delete the link from the edit business profile
     *
     * @param  \App\Http\Requests\UpdateBusinessRequest  $request
     */
    public function delete_external_link(Request $request)
    {
        //
        # code...
        $validator = Validator::make($request->all(), [
            'external_link_id' => 'required|string|'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $external_link_id = request('external_link_id');

        BusinessExternalLinks::where('id', $external_link_id)->delete();

        return response()->json([
            'message' => 'External Link deleted successfully',
            'status' => true,
            'data' => (object)[]
        ], 200);
    }

    public function isBusinessRegisterd($user_id)
    {
        # code...

        $business = Business::where('user_id', $user_id)->first();
        // if no user found in database

        if ($business == null) {
            return false;
        }
        return true;
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

        $name = 'business/' . Str::random(15) . '.png';

        Storage::put('public/' . $name, $fileData);
        // update the user's profile_pic
        return $name;
    }
}
