<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Models\Notification;
use App\Notifications\SendPushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function dashboard(){
        $newOrders =  array();
        $customers = User::where('role_id', 2)->count();
        $products =  array();

        if(Auth::user()->role_id == 3){
            return view('staff.dashboard', compact('newOrders'));
        }
        return view('admin.dashboard', compact('newOrders', 'customers', 'products'));
    }

    public function setting(Request $request){
        $content = Setting::firstOrFail();
        if ($request->method() == 'POST'){
            $content->title = $request->input('title') ?? 'Pizzeria';
            $content->email = $request->input('email');
            $content->phone = $request->input('phone');
            $content->address = $request->input('address');
            $content->facebook = $request->input('facebook');
            $content->twitter = $request->input('twitter');
            $content->instagram = $request->input('instagram');
            $content->currency = $request->input('currency');
            $content->save();
            try{
                if ($request->file('logo')) {
                    $fileName = time() . '-' . $request->file('logo')->getClientOriginalName();
                    $filePath = $request->file('logo')->path();
                    $imageUrl = $this->uploadImageIK($fileName, $filePath, 'setting');
                    $content->logo = $imageUrl ?? null;
                }
                $content->save();
            }catch (\Exception $ex){
                return redirect()->back()->with('error', 'Exception in while uploading image');
            }
            return redirect()->back()->with('success', 'Site Setting Updated Successfully');
        }
        return view('admin.setting', compact('content'));
    }

    public function showNotification(){
        try {
            if (request()->ajax()) {
                return datatables()->of(Notification::orderBy('id', 'desc')->get())
                    ->addIndexColumn()
                    ->addColumn('date', function ($data) {
                        return $data->created_at->format('Y-m-d');
                    })->rawColumns(['date'])->make(true);
            }
        } catch (\Exception $ex) {
            return redirect('/dashboard')->with('error', $ex->getMessage());
        }
        return view('admin.notifaction');
    }

    public function updateNotification(Request $request){
        $notification = Notification::find($request->id??0);
        if ($notification != null){
            $notification->read_at = date('Y-m-d H:i:s');
            $notification->save();
            return true;
        }
        return false;
    }

    public function updateToken(Request $request){
        try{
            $request->user()->update(['fcm_token'=>$request->token]);
            return response()->json([
                'success'=>true
            ]);
        }catch(\Exception $e){
            report($e);
            return response()->json([
                'success'=>false
            ],500);
        }
    }

    public function sendNotification(){
        $resp = $this->sendPushNotification('Test', 'Test Notification');
        return $resp;
    }
}
