<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function dashboard(){
        $newOrders =  Order::where('status', 'Pending')->count();
        $restaurants = Restaurant::all();
        $data = array(
            'new_orders' => $newOrders,
            'restaurants' => $restaurants,
        );
        return $this->success($data);
    }

    public function setting(Request $request){
        $content = Setting::first();
        if ($request->method() == 'POST'){
            $content->title     = $request->title ?? $content->title;
            $content->email     = $request->email ?? $content->email;
            $content->phone     = $request->phone ?? $content->phone;
            $content->address   = $request->address ?? $content->address;
            $content->facebook  = $request->facebook ?? $content->facebook;
            $content->twitter   = $request->twitter ?? $content->twitter;
            $content->instagram = $request->instagram ?? $content->instagram;
            $content->currency  = $request->currency ?? $content->currency;
            $content->save();
            try{
                if ($request->has('logo') && $request->logo != null){
                    $imageUrl = $this->uploadImage($request->file('logo'), 'uploads/settings/');
                    $content->logo = $imageUrl;
                }
                $content->save();
            }catch (\Exception $ex){
                $this->error('Exception occurred n while uploading image');
            }
            return $this->success($content, 'Site Setting Updated Successfully');
        }
        return $this->success($content);
    }

    public function showNotification(){
        $data = Notification::where('read_at', null)->orderBy('id', 'desc')->limit(20)->get();
        return $this->success($data);
    }

    public function updateNotification(Request $request){
        $notification = Notification::find($request->id??0);
        if ($notification != null){
            $notification->read_at = date('Y-m-d H:i:s');
            $notification->save();
            return $this->success([], 'Notification status updated');
        }
        return $this->error("Not Found");
    }

    public function updateToken(Request $request){
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required'
        ]);
        if ($validator->fails()){
            return $this->error('Validation Error', 200, $validator->errors());
        }
        $user = Auth::user();
        $user->fcm_token = $request->fcm_token;
        $user->save();
        return $this->success($user, 'FCM Token Updated Successfully.');
    }
}
