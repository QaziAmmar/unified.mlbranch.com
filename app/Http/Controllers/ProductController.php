<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\FavouriteProducts;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\RecentProduct;
use App\Models\User;
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
            'description' => 'required|string|',
            'product_images' => 'required'
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

        $product['product'] = Product::where('id', $product_id)
        ->with("product_images")
        ->with('business')
        ->first();
        $product['related_product'] = $this->get_related_product();
        

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

        $products = Product::where('business_id', $business_id)->with("product_images")->get();

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
    public function like_dislike()
    {
        # code...

        $validator = Validator::make(request()->all(), [
            'user_id' => 'required|string',
            'product_id' => 'required|string|',
            'status' => 'required|string|',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        // show error if no user of product is found againts IDs.
        if ((User::find(request('user_id'))) == null) {
            return $this->general_error_with('No user found against this ID');
        }
        if ((Product::find(request('product_id'))) == null) {
            return $this->general_error_with('No product found against this ID');
        }

        if (request('status') == "1") {
            return $this->add_favourite(request('user_id'), request('product_id'));
        } else {
            return $this->delete_favourite(request('user_id'), request('product_id'));
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
        # code...
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'price' => 'required|string|',
            'title' => 'required|string|',
            'description' => 'required|string|',
            'product_images' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $product = Product::find(request('product_id'));
        $data = request(['business_id']);
        // check which fields are set for update then only update those fields.
        $data = $this->generate_date_for_update($data, request('product_id'));
        
        $product->update($data);

        return response()->json([
            'message' => 'Product updated successfully',
            'status' => true,
            'data' => $product
        ], 200);
    }

    // CUSTOM FUNCTION EXTENSION

    public function generate_date_for_update($date, $product_id)
    {
        # code...
        if (request('price') != null) {
            $date['price'] = request('price');
        }
        if (request('title') != null) {
            $date['title'] = request('title');
        }
        if (request('description') != null) {
            $date['description'] = request('description');
        }
        if (request('product_images') != null) {
            $date['product_images'] = $this->save_product_images(request('product_images'), $product_id);
        }

        return $date;
    }

    public function get_related_product()
    {
        # code...
        return Product::limit(8)
        ->with('product_image')
        // ->join('businesses', 'businesses.id', '=', 'products.business_id')
        // ->select('products.id', 'products.title', 'products.price')
        // ->withCount('like')
        // ->orderBy('products.created_at', 'ASC')
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
