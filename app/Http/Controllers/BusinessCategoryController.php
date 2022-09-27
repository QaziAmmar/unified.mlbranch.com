<?php

namespace App\Http\Controllers;

use App\Models\BusinessCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BusinessCategoryController extends Controller
{
    //

    public function create(Request $request)
    {
        //
        # code...
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'image' => 'required|string|',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $category = request(['name']);
        $category['image'] = $this->save_base64_image(request('image'));

        // first create the product and then get the product id
        $category = BusinessCategory::create($category);
        if ($category == null) {
            return $this->general_error_with("product creation fail");
        }

        
        return response()->json([
            'message' => 'Category created successfully',
            'status' => true,
            'data' => $category
        ], 200);
    }



    // Custom Function Extension.

    public function save_base64_image($base64File)
    {
        # code...
        $fileData = base64_decode($base64File);

        $name = 'business_category/' . Str::random(15) . '.png';

        Storage::put('public/' . $name, $fileData);
        // update the user's profile_pic
        return $name;
    }


}
