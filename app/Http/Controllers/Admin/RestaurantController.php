<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    public function index(){
        try {
            if (request()->ajax()) {
                return datatables()->of(Restaurant::orderBy('id', 'desc')->get())
                    ->addColumn('image', function ($data) {
                        return '<img class="cell-image" src="'.$this->getImage($data->image).'" width="40" height="40">';
                    })
                    ->addColumn('action', function ($data) {
                        return '<a title="edit" href="' . route('admin.editRestaurant',$data->id) . '" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>&nbsp;<button title="Delete" type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
                    })->rawColumns(['image','action'])->make(true);
            }
        } catch (\Exception $ex) {
            return redirect('/dashboard')->with('error', $ex->getMessage());
        }
        return view('admin.restaurants.index');
    }

    public function create(Request $request){
        if ($request->method() == 'POST'){
            $this->validate($request, [
                'name' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'address' => 'required',
                'city' => 'required',
            ]);

            $content = Restaurant::create([
                'name' => $request->input('name'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'country' => $request->input('country'),
                'slug' => $this->createSlug($request->input('name'))
            ]);

            if ($content != null){
                $password = $content->slug.rand(1000,99999);
                User::create([
                    'name' => $request->input('name'),
                    'phone' => $request->input('phone'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($password),
                    'role_id' => 3,
                    'role_name' => 'Restaurant',
                    'status' => 1,
                    'is_restaurant' => 1,
                    'restaurant_id' => $content->id,
                ]);
                try{
                    if ($request->has('image')){
                        $imageUrl = $this->uploadImage($request->file('image'), 'uploads/restaurants/');
                        $content->update(['image' => $imageUrl]);
                    }
                    return redirect()->back()->with('success', 'Restaurant Added Successfully');
                }catch (\Exception $ex){
                    return redirect()->back()->with('error', 'Exception in while uploading image');
                }
            }
            return redirect()->back()->with('error', 'Exception!! Something Went Wrong.');
        }
        return view('admin.restaurants.create');
    }

    public function edit(Request $request, $id){
        $content = Restaurant::findOrFail($id);
        if ($request->method() == 'POST'){
            $this->validate($request, [
                'name' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'address' => 'required',
                'city' => 'required',
            ]);

            $content->name = $request->input('name');
            $content->phone = $request->input('phone');
            $content->email = $request->input('email');
            $content->address = $request->input('address');
            $content->city = $request->input('city');
            $content->state = $request->input('state');
            $content->country = $request->input('country');
            $content->save();
            try{
                if ($request->has('image')){
                    $imageUrl = $this->uploadImage($request->file('image'), 'uploads/restaurants/');
                    $content->image = $imageUrl ?? $content->image;
                }
                $content->save();
            }catch (\Exception $ex){
                return redirect()->back()->with('error', 'Exception in while uploading image');
            }
            return redirect()->back()->with('success', 'Restaurant Updated Successfully');
        }
        return view('admin.restaurants.create', compact('content'));
    }

    public function destroy($id){
        $content = Restaurant::find($id);
        if ($content != null){
            $content->delete();
            return true;
        }
        return false;
    }

    private function createSlug($str){
        $slug = Str::slug($str, '-');
        if (Restaurant::whereSlug($slug)->exists()) {
            $original = $slug;
            $count = 1;

            while (Restaurant::whereSlug($slug)->exists()) {
                $slug = "$original-" . $count++;
            }
        }
        return $slug;
    }
}
