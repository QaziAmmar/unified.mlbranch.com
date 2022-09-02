<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Business;
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
            'business_img' => 'required|string|'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $business = request(['user_id', 'name', 'location_name', 'lat', 'long', 'description']);
        // convert the image into base 64 and save it into the folder.
        $business['bannar_img'] = $this->save_base64_image(request('bannar_img'));
        $business['business_img'] = $this->save_base64_image(request('business_img'));

        $business = Business::create($business);

        return response()->json([
            'message' => 'Business Created successfully',
            'status' => false,
            'data' => $business
        ], 200);


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBusinessRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function show(Business $business)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function edit(Business $business)
    {
        //
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\Response
     */
    public function destroy(Business $business)
    {
        //
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

    public function save_base64_image($base64File)
    {
        # code...
         $fileData = base64_decode($base64File);
 
         $name = 'users_profile/' . Str::random(15) . '.png';

         Storage::put('public/' . $name, $fileData);
         // update the user's profile_pic
         return $name;
    }


}
