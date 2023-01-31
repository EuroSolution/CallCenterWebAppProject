<?php

namespace App\Http\Controllers\Api\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(){
        $products = Product::with('category')->get();
        return $this->success($products);
    }

    public function add(Request $request){
        if ($request->method() == 'POST'){
            $this->validate($request, array(
                'name' => 'required',
                'category' => 'required',
                'restaurant' => 'required',
                'price'	   => 'numeric'
            ));

            $product = Product::create([
                'name' => $request->input('name'),
                'category_id' => $request->input('category'),
                'restaurant_id' => $request->input('restaurant') ?? 0,
                'description' => $request->input('description'),
                'price' => $request->input('price') ?? 0.00,
                'slug' => $this->createSlug($request->input('name')),
                'type' => $request->input('type')
            ]);

            if (!empty($request->get('sizes'))){
                foreach ($request->get('sizes') as $sKey => $size){
                    if ($size != null){
                        ProductSize::create([
                            'product_id' => $product->id,
                            'size' => $size,
                            'price' => $request->get('size_prices')[$sKey] ?? 0,
                            'discounted_price' => 0.00,
                        ]);
                    }
                }
            }

            if ($request->has('file')){
                $imageUrl = $this->uploadImage($request->file('file'), 'uploads/products/');
                $product->update(['image' => $imageUrl]);
            }
            return redirect()->back()->with(['success' => 'Product Added Successfully']);
        }

        $categories = Category::get();
        $restaurants = Restaurant::where('status', 1)->get();
        return view('call-center.product.add-product', compact('categories', 'restaurants'));
    }


    public function show($id)
    {
        $product = Product::where('id', $id)->firstOrFail();
        return view('call-center.product.view', compact('product'));
    }


    public function edit(Request $request, $id)
    {
        $content = Product::with('productSizes')->findOrFail($id);
        if ($request->method() == "POST"){
            $this->validate($request, array(
                'name' => 'required',
                'category' => 'required',
                'restaurant' => 'required',
                'price'	   => 'numeric'
            ));

            $content->name = $request->input('name');
            $content->description = $request->input('description');
            $content->restaurant_id = $request->input('restaurant');
            $content->price = $request->input('price');
            $content->type = $request->input('type');
            if ($request->has('file')){
                $imageUrl = $this->uploadImage($request->file('file'), 'uploads/products/');
                $content->image = $imageUrl;
            }
            $content->save();

            ProductSize::where('product_id', $id)->delete();
            if (!empty($request->get('sizes'))){
                foreach ($request->get('sizes') as $sKey => $size){
                    ProductSize::create([
                        'product_id' => $id,
                        'size' => $size,
                        'price' => $request->get('size_prices')[$sKey] ?? 0,
                        'discounted_price' => 0.00,
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Product Updated successfully');
        }
        $restaurants = Restaurant::where('status', 1)->get();
        $categories = Category::get();
        return view('call-center.product.update-product', compact('content','categories', 'restaurants'));
    }

    public function destroy($id)
    {
        $content = Product::find($id);
        $content->delete();
        echo 1;
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
}
