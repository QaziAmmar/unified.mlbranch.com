<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImages;
use App\Models\RecentProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function detail()
    {
        # code...

        $user_id = request('user_id');

        $banner_images = $this->banner_images();
        $feature_product = $this->feature_product();
        $recent_products = $this->get_recent_product_of();

        return response()->json([
            'message' => 'Shop data',
            'status' => true,
            'data' => [
                'banner_images' => $banner_images,
                'feature_product' => $feature_product,
                'recent_products' => $recent_products
            ]
        ], 200);
    }




    public function banner_images()
    {
        # code...
        // This is function is under testing and for time being we getting data from product tables.
        return ProductImages::latest()->take(3)->get()->pluck('image_link');
    }
    /**
     * Get random top 10 products from the database.
     */
    public function feature_product()
    {
        # code...
        // get the products that are featured.
        return Product::inRandomOrder()
            ->whereHas('business', function ($business) {
                $business->where('is_featured', '=', true);
            })
            ->with("business")
            ->with("product_images")
            ->limit(10)
            ->get();
    }
    /**
     * Get the recent selected top 5 products form the database.
     */
    public function get_recent_product_of()
    {
        # code...
        return Product::limit(8)
            ->with('business')
            ->with("product_images")
            ->orderBy('products.created_at', 'ASC')
            ->get();

        // return RecentProduct::where('user_id', $user_id)
        //     ->groupBy('product_id')
        //     ->limit(30)
        //     ->get();
    }
}
