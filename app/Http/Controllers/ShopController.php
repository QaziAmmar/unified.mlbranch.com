<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\RecentProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    /**
     * This function will gather the data from different tables
     * and show them in user's shop tab.
     * Need to @return 3 thing.
      1. Banner Image
      2. Feature Products 
      3. Recent products
     */

    public function home()
    {
        # code...

        $user_id = request('user_id');

        $banner_images = $this->banner_images();
        $feature_businesses = $this->feature_business();
        $business_categories = $this->get_business_categories();

        return response()->json([
            'message' => 'Shop data',
            'status' => true,
            'data' => [
                'banner_images' => $banner_images,
                'feature_business' => $feature_businesses,
                'business_categories' => $business_categories
            ]
        ], 200);
    }

    public function business_list_by_category()
    {
        # code...
        $validator = Validator::make(request()->all(), [
            'user_id' => 'required|string',
            'category_id' => 'required|string|',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $category_id = request('category_id');

        $business = Business::inRandomOrder()
            ->where('is_featured', '=', false)
            ->where('category_id', '=', $category_id)
            ->orderBy('created_at', 'ASC')
            ->limit(10)
            ->get();

        return response()->json([
            'message' => 'Business List by Category',
            'status' => true,
            'data' => [
                'business' => $business
            ]
        ], 200);
    }

    public function test()
    {
        # code...

        # code...
        $validator = Validator::make(request()->all(), [
            'user_id' => 'required|string',
            'category_id' => 'required|string|',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $category_id = request('category_id');

        $business = Business::inRandomOrder()
            ->where('is_featured', '=', false)
            ->where('category_id', '=', $category_id)
            ->orderBy('created_at', 'ASC')
            ->limit(10)
            ->get();

        return response()->json([
            'message' => 'Business List by Category',
            'status' => true,
            'data' => [
                'business' => $business
            ]
        ], 200);
    }



    // CUSTOM FUNCTION 

    public function banner_images()
    {
        # code...
        // these banner image will be uploaed form the server side and will shown here.
        // This is function is under testing and for time being we getting data from product tables.
        return ProductImages::latest()->take(3)->get()->pluck('image_link');
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
    /**
     * Get random top 10 products from the database.
     */
    public function feature_business()
    {
        # code...

        // get the products that are featured.
        return Business::inRandomOrder()
            ->where('is_featured', '=', true)
            ->limit(10)
            ->get();

        // wherehas Query
        // Product::inRandomOrder()
        //     ->whereHas('business', function ($business) {
        //         $business->where('is_featured', '=', true);
        //     })
        //     ->with("business")
        //     ->with("product_images")
        //     ->limit(10)
        //     ->get();

    }
    /**
     * Get the recent selected top 5 products form the database.
     */
    public function get_business_categories()
    {
        # code...
        return BusinessCategory::get();

        // return RecentProduct::where('user_id', $user_id)
        //     ->groupBy('product_id')
        //     ->limit(30)
        //     ->get();
    }
}
