<?php

namespace App\Http\Controllers\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\ProductSize;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;



class ProductController extends Controller
{
    public function index(){
        try {
            if (request()->ajax()) {
                return datatables()->of(Product::with('category')->get())
                    ->addColumn('image', function ($data) {
                        return '<img class="cell-image" src="'.$this->getImage($data->image).'" width="40" height="40">';
                    })
                    ->addColumn('category_id', function ($data) {
                        return $data->category->name ?? '';
                    })
                    ->addColumn('action', function ($data) {
                        return '<a title="View" href="product/show/' . $data->id . '" class="btn btn-dark btn-sm"><i class="fas fa-eye"></i></a>&nbsp;<a title="edit" href="product/edit/' . $data->id . '" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>&nbsp;<button title="Delete" type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
                    })->rawColumns(['image','action'])->make(true);
            }
        } catch (\Exception $ex) {
            return redirect('/dashboard')->with('error', $ex->getMessage());
        }
        return view('call-center.product.index');
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

    public function getProductsByRestaurantId($id){
        $products = Product::with('productSizes')->where('restaurant_id', $id)->select('id', 'name', 'price')->get();
        return array('products' => $products);
    }

    public function getProductSizes($id){
        $sizes = ProductSize::where('product_id', $id)->select('size', 'price')->get();
        if ($sizes == null){
            return array('size' => 'small', 'price' => 0);
        }
        return $sizes;
    }
}
