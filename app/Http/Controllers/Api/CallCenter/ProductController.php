<?php

namespace App\Http\Controllers\Api\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request){
        $products = Product::with('category');
        if ($request->restaurant_id != null){
            $products = $products->where('restaurant_id', $request->restaurant_id);
        }
        return $this->success($products->get());
    }

    public function add(Request $request){
    
        $validator = Validator::make($request->all(), array(
            'name' => 'required',
            'category_id' => 'required',
            'restaurant_id' => 'required',
            'price'	   => 'numeric'
        ));
        if ($validator->fails()){
            return $this->error("Validation Error", 200, $validator->errors());
        }

        //Save base64 image
        $imageUrl = '';
        if(isset($request->image)){
            $imageUrl = $this->uploadEncodedImage($request->image, 'products/');
        }

        $product = Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'restaurant_id' => $request->restaurant_id ?? 0,
            'description' => $request->description,
            'price' => $request->price ?? 0.00,
            'slug' => $this->createSlug($request->name),
            'type' => $request->type,
            'image' => $imageUrl
        ]);

        if (!empty($request->sizes)){
            foreach ($request->sizes as $sKey => $size){
                if ($size != null){
                    ProductSize::create([
                        'product_id' => $product->id,
                        'size' => $size,
                        'price' => $request->size_prices[$sKey] ?? 0,
                        'discounted_price' => 0.00,
                    ]);
                }
            }
        }

//        if ($request->has('file')){
//            $imageUrl = $this->uploadImage($request->file('file'), 'uploads/products/');
//            $product->update(['image' => $imageUrl]);
//        }

        return $this->success($product, 'Product Added Successfully');
    }


    public function show(Request $request){
        $product = Product::find($request->id);
        return $this->success($product);
    }


    public function edit(Request $request, $id){

        $content = Product::with('productSizes')->find($id);

        $content->name = $request->name ?? $content->name;
        $content->category_id = $request->category_id ?? $content->category_id;
        $content->description = $request->description ?? $content->description;
        $content->restaurant_id = $request->restaurant_id ?? $content->restaurant_id;
        $content->price = $request->price ?? $content->price;
        $content->type = $request->type ?? $content->type;
//            if ($request->has('file')){
//                $imageUrl = $this->uploadImage($request->file('file'), 'uploads/products/');
//                $content->image = $imageUrl;
//            }
        $content->save();

        ProductSize::where('product_id', $id)->delete();
        if (!empty($request->sizes)){
            foreach ($request->sizes as $sKey => $size){
                ProductSize::create([
                    'product_id' => $id,
                    'size' => $size,
                    'price' => $request->size_prices[$sKey] ?? 0,
                    'discounted_price' => 0.00,
                ]);
            }
        }
        return $this->success($content, 'Product Updated successfully');
    }

    public function destroy(Request $request)
    {
        $content = Product::find($request->id);
        if ($content != null) {
            $content->delete();
            return $this->success([], "Product deleted successfully");
        }else{
            return $this->error("Product not found");
        }
    }

    private function createSlug($str){
        $slug = Str::slug($str, '-');
        if (Product::whereSlug($slug)->exists()) {
            $original = $slug;
            $count = 1;

            while (Product::whereSlug($slug)->exists()) {
                $slug = "$original-" . $count++;
            }
        }
        return $slug;
    }

    public function getProductsByRestaurantId(Request $request, $restaurantId){

        $products = Product::with(['category','productSizes'])->where('restaurant_id',$restaurantId)->get();
        return $this->success($products);
    }
}
