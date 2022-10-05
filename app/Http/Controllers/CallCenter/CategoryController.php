<?php

namespace App\Http\Controllers\CallCenter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use ImageKit\ImageKit;
use Nette\Utils\Image;

class CategoryController extends Controller
{
    public function index(){
        try {
            if (request()->ajax()) {
                return datatables()->of(Category::with('subCategory')->get())
                    ->addColumn('parent_id', function ($data) {
                        return $data->subCategory->name ?? '--';
                    })
                    ->editColumn('image', function ($data) {
                        return '<img class="cell-image" src="'.$this->getImage($data->image).'" width="40" height="40">';
                    })
                    ->addColumn('action', function ($data) {
                        return '<a title="View" href="category/show/' . $data->id . '" class="btn btn-dark btn-sm"><i class="fas fa-eye"></i></a>&nbsp;<a title="edit" href="category/edit/' . $data->id . '" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>&nbsp;<button title="Delete" type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
                    })->rawColumns(['parent_id', 'image', 'action'])->make(true);
            }
        } catch (\Exception $ex) {
            return redirect('/dashboard')->with('error', $ex->getMessage());
        }
        return view('call-center.category.list');
    }

    public function add(Request $request){
        if ($request->method() == 'POST') {
            $this->validate($request, array(
                'name' => 'required|unique:categories'
            ));

            $slugStr = Str::of($request->input('name'))->slug('-');

            $category = Category::create([
                'name' => $request->input('name'),
                'parent_id' => $request->input('main-category'),
                'slug' => $this->createSlug($slugStr),
                'image' => $imageUrl ?? null
            ]);
            if ($request->file('file')) {
                $imageUrl = $this->uploadImage($request->file('file'), 'uploads/categories/');
                $category->update(['image' => $imageUrl]);
            }

            return redirect()->back()->with(['success' => 'Category Added Successfully']);
        }
        $mainCategories = Category::where('parent_id', 0)->get();
        return view('call-center.category.add-category', compact('mainCategories'));
    }


    public function show(int $id){

        $content= Category::with('subCategory')->find($id);
        return view('admin.category.view',compact('content'));
    }

    public function edit(Request $request, $id){
        if ($request->method() == 'POST') {
            $this->validate($request, array(
                'name' => ['required', Rule::unique('categories')->ignore($id)]
            ));

            if ($request->has('main-category') && $request->get('main-category') != 0){
                $main_category = $request->input('main-category');
                $mainCategory = Category::find($main_category);
                if($mainCategory->name == $request->input('name')){
                    return redirect()->back()->with(['err'=> "Parent and Child Category can't be same"])->withInput();
                }
            }
            $category = Category::find($id);

            if ($request->file('file')) {
                $imageUrl = $this->uploadImage($request->file('file'), 'uploads/categories/');
                $category->image = $imageUrl;
            }

            $category->name = $request->input('name');
            $category->parent_id = $request->input('main-category');
            $category->save();
            return redirect()->back()->with(['success' => 'Category Edit Successfully']);

        }else {
            $content = Category::findOrFail($id);
            $mainCategories = Category::where('parent_id', 0)->get();
            return view('call-center.category.add-category', compact('mainCategories','content'));
        }
    }

    public function destroy(int $id){
        $content = Category::find($id);
        if($content->parent_id == 0){
            $count = Category::where('parent_id',$id)->count();
            if($count == 0){
                $content->delete();
                echo 1;
            }else{
                echo 0;
            }
        } else{
            $content->delete();
            echo 1;
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
