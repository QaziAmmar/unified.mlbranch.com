<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\RecentProduct;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;



class ProductController extends Controller
{

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
            'description' => 'required|string|'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $product = request(['business_id', 'price', 'title', 'description']);

        // first create the product and then get the product id
        $product = Product::create($product);
        if ($product == null) {
            return $this->general_error_with("product creation fail");
        }
        
        $product['product_images'] = $this->save_product_images(request('product_images'), $product->id);


        return response()->json([
            'message' => 'Product added successfully',
            'status' => true,
            'data' => $product
        ], 200);
    }


    public function detail()
    {
        $user_id = request('user_id');
        $product_id = request('product_id');

        // show the histor of recent selected products
        RecentProduct::create(['user_id' => $user_id, 'product_id' => $product_id]);

        $product = Product::where('id', $product_id)->with("post_images")->get();

        if ($product == null) {
            return $this->general_error_with("No product found");
        } else {
            // $product_images = ProductImages::where('product_id', $product->id)->get();
            // $product['image'] = $product_images;
            return response()->json([
                'message' => 'Product found successfully',
                'status' => true,
                'data' => $product
            ], 200);
        }
        
    }

    public function all()
    {

        // $user_id = request('user_id');
        $business_id = request('business_id');

        $products = Product::where('business_id', $business_id)->with("post_images")->get();

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


    public function save_base64_image($base64File)
    {
        # code...
        $fileData = base64_decode($base64File);

        $name = 'product/' . Str::random(15) . '.png';

        Storage::put('public/' . $name, $fileData);
        // update the user's profile_pic
        return $name;
    }
}
