<?php

namespace App\Http\Controllers\Api\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(){
        $data = array();
        $categories = Category::with('parentCategory')->get();

        foreach ($categories as $category){
            $data[] = array(
                'id' => $category->id,
                'image' => $this->getImage($category->image),
                'parent_category' => $category->parentCategory != null ? $category->parentCategory->name : '--',
                'slug' => $category->slug
            );
        }

        return $this->success($data);
    }

    public function getMainCategories(){
        $mainCategories = Category::where('parent_id', 0)->get();
        $data = array();
        foreach ($mainCategories as $category){
            $data[] = array(
                'id' => $category->id,
                'image' => $this->getImage($category->image),
                'parent_category' => $category->parentCategory != null ? $category->parentCategory->name : '--',
                'slug' => $category->slug
            );
        }
        return $this->success($data);
    }

    public function add(Request $request){

        $validator = Validator::make($request->all(), array(
            'name' => 'required|unique:categories'
        ));
        if ($validator->fails()){
            return $this->error("Validation Error", 200, [], $validator->errors());
        }

        $slugStr = Str::of($request->name)->slug('-');

        //Save base64 image
        $imageUrl = '';
        if(isset($request->image)){
            $imageUrl = $this->uploadEncodedImage($request->image, 'categories/');
        }

        $category = Category::create([
            'name' => $request->name,
            'parent_id' => $request->main_category,
            'slug' => $this->createSlug($slugStr),
            'image' => $imageUrl ?? ""
        ]);
//        if ($request->file('file')) {
//            $imageUrl = $this->uploadImage($request->file('file'), 'uploads/categories/');
//            $category->update(['image' => $imageUrl]);
//        }

        return $this->success($category, 'Category Added Successfully');

    }


    public function show(Request $request){

        $content= Category::with('subCategory')->find($request->id);
        return $this->success($content);
    }

    public function edit(Request $request){
        $id = $request->id;
        $validator = Validator::make($request->all(), array(
            'name' => ['required', Rule::unique('categories')->ignore($id)]
        ));
        if ($validator->fails()){
            return $this->error("Validation Error", 200, [], $validator->errors());
        }

        if ($request->main_category && $request->main_category != 0){
            $main_category = $request->main_category;
            $mainCategory = Category::find($main_category);
            if($mainCategory->name == $request->name){
                return $this->error("Parent and Child Category can not be same");
            }
        }
        $category = Category::find($id);

//        if ($request->file('file')) {
//            $imageUrl = $this->uploadImage($request->file('file'), 'uploads/categories/');
//            $category->image = $imageUrl;
//        }

        $category->name = $request->name;
        $category->parent_id = $request->main_category ?? 0;
        $category->save();
        return $this->success($category, 'Category updated successfully');
    }

    public function destroy(Request $request){
        $id = $request->id;
        $content = Category::find($id);
        if ($content != null) {
            $content->delete();
            return $this->success([], "Category Deleted Successfully");
        }else{
            return $this->error("Not Found");
        }
    }

    private function createSlug($str){
        $checkSlug = Category::where('slug', $str)->exists();
        if ($checkSlug) {
            $number = 1;
            while ($number) {
                $newSlug = $str . "-" . $number++;
                $checkSlug = Category::where('slug', $newSlug)->exists();
                if (!$checkSlug) {
                    $slug = $newSlug;
                    break;
                }
            }
        } else {
            $slug = $str;
        }
        return $slug;
    }
}
