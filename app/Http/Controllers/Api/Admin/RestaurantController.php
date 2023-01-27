<?php

namespace App\Http\Controllers\Api\Admin;

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
        $data = Restaurant::orderBy('id', 'desc')->get();
        $this->success($data);
    }

    public function create(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'address' => 'required',
            'city' => 'required',
        ]);
        if ($validator->fails()){
            return $this->error("Validation Error", $validator->errors());
        }

        $content = Restaurant::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'slug' => $this->createSlug($request->name)
        ]);

        if ($content != null){
            $password = $content->slug.rand(1000,99999);
            User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
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

            }catch (\Exception $ex){
                return $this->error('Exception occurred while uploading image');
            }
        }
        return $this->success($content);
    }

    public function edit(Request $request, $id){
        $content = Restaurant::find($id);
        if ($content != null) {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'phone' => 'required',
                'email' => 'required',
                'address' => 'required',
                'city' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->error("Validation Error", $validator->errors());
            }

            $content->name      = $request->name ?? $content->name;
            $content->phone     = $request->phone ?? $content->phone;
            $content->email     = $request->email ?? $content->email;
            $content->address   = $request->address ?? $content->address;
            $content->city      = $request->city ?? $content->city;
            $content->state     = $request->state ?? $content->state;
            $content->country   = $request->country ?? $content->country;
            $content->save();
            try {
                if ($request->has('image')) {
                    $imageUrl = $this->uploadImage($request->file('image'), 'uploads/restaurants/');
                    $content->image = $imageUrl ?? $content->image;
                }
                $content->save();
            } catch (\Exception $ex) {
                return $this->error('Exception occurred in while uploading image');
            }
            return $this->success($content,'Restaurant Updated Successfully');
        }
        return $this->error('Restaurant Not Found');
    }

    public function destroy($id){
        $content = Restaurant::find($id);
        if ($content != null){
            $content->delete();
            return $this->success([],'Restaurant Deleted Successfully');
        }
        return $this->error('Restaurant Not Found');
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
