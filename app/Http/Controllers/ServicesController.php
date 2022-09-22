<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Business;
use App\Models\FavouriteProducts;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\RecentProduct;
use App\Models\Services;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServicesController extends Controller
{
    //
     //
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
            'business_id' => 'required',
            'price' => 'required|string|',
            'title' => 'required|string|',
            'description' => 'required|string|',
            'duration' => 'required|string|',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $service = request(['business_id', 'price', 'title', 'description', 'duration']);

        // first create the product and then get the product id
        $service = Services::create($service);
        if ($service == null) {
            return $this->general_error_with("product creation fail");
        }
        
        return response()->json([
            'message' => 'Service added successfully',
            'status' => true,
            'data' => $service
        ], 200);
    }

    public function detail()
    {
        $user_id = request('user_id');
        $service_id = request('service_id');


        $service = Services::where('id', $service_id)
        ->with('business')->first();

        if ($service == null) {
            return $this->general_error_with("No Service found");
        } else {
            
            return response()->json([
                'message' => 'Service found successfully',
                'status' => true,
                'data' => $service
            ], 200);
        }
    }

    public function all()
    {

        // $user_id = request('user_id');
        $business_id = request('business_id');

        $products = Services::where('business_id', $business_id)->get();

        if (count($products) == 0) {
            return response()->json([
                'message' => 'No product found',
                'status' => false,
                'data' => []
            ], 401);
        } else {
            return response()->json([
                'message' => 'Product found successfully',
                'status' => true,
                'data' => $products
            ], 200);
        }
    }
    // Like of dislike a product for user.

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

        $business = Business::find(request('business_id'));


        // convert the image into base 64 and save it into the folder.
        $date = request(['business_id', 'name', 'location_name', 'lat', 'long', 'description']);

        $date['bannar_img'] = $this->save_base64_image(request('bannar_img'));
        $date['business_img'] = $this->save_base64_image(request('business_img'));


        $business->update($date);

        return response()->json([
            'message' => 'Business updated successfully',
            'status' => true,
            'data' => $business
        ], 200);
    }

    public function get_related_product()
    {
        # code...
        return Product::limit(8)
        ->with("post_images")
        ->join('businesses', 'businesses.id', '=', 'products.business_id')
        ->orderBy('products.created_at', 'ASC')
        ->get();
    }

    public function isBusinessRegisterd($user_id)
    {
        # code...

        $user = Business::where('user_id', $user_id);
        // if no user found in database
        if ($user == null) {
            return false;
        }
        return true;
    }

    public function add_favourite($user_id, $product_id)
    {
        # code...
        $data = [
            'user_id' => $user_id,
            'product_id' => $product_id,
        ];

        if ($this->is_already_favourite($user_id, $product_id)) {
            return $this->general_error_with("product already liked");
        }

        $product = FavouriteProducts::create($data);
    
        if ($product == null) {
            return $this->general_error_with("product creation fail");
        }

        return response()->json([
            'message' => 'Product liked successfully',
            'status' => true,
            'data' => (object)[]
        ], 200);
    }

    public function delete_favourite($user_id, $product_id)
    {
        # code...

        $status = FavouriteProducts::where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->delete();

        if ($status == null) {
            return $this->general_error_with("Product is already disliked");
        }

        return response()->json([
            'message' => 'Product disliked successfully',
            'status' => true,
            'data' => (object)[]
        ], 200);
    }

    public function is_already_favourite($user_id, $product_id)
    {
        # code...
        $product = FavouriteProducts::where('user_id', $user_id)
            ->where('product_id', $product_id)->first();

        if ($product != null) {
            return true;
        } else {
            return false;
        }
    }

    public function save_product_images($product_images, $product_id)
    {

        if ($product_images == null) {
            return [];
        }
        $save_product_images = [];
        foreach ($product_images as $image) {


            $image_link = $this->save_base64_image($image);

            $data = [
                'product_id' => $product_id,
                'image_link' => $image_link
            ];
            $product_img = ProductImages::create($data);

            array_push($save_product_images, $product_img);
        }
        return $save_product_images;
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

}
