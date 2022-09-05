<?php

namespace App\Http\Controllers;

use App\Models\ProductImages;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductImagesController extends Controller
{
    //


    public function create($product_images, $product_id)
    {
        if ($product_images == null) {
            return;
        }

        $product_images = [];

        foreach ($product_images as $image) {
            # code...
            $image_link = $this->save_base64_image($image);
            
            $data = [
                'product_id' => $product_id,
                'image_link' => $image_link
            ];
            $product_img = ProductImages::create($data);

            array_push($product_images, $product_img);
        }
        return $product_images;
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
