<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        if(Auth::check()){
            if(Auth::user()->role_id == 1){
                return redirect(route('admin.dashboard'));
            } elseif(Auth::user()->role_id == 2){
                return redirect(route('callCenter.dashboard'));
            } elseif(Auth::user()->role_id == 3){
                return redirect(route('restaurant.dashboard'));
            }
        }
        if ($request->method() == 'POST'){
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);
            if ($validator->fails()){
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $user = User::where('email', $request->input('email'))->first();
            if ($user != null){
                if (Hash::check($request->input('password'), $user->password)) {
                    Auth::login($user);
                    $user->update(['fcm_token'=>$request->token]);

                    if($user->role_id == 1){
                        return redirect(route('admin.dashboard'));
                    }elseif($user->role_id == 2){
                        return redirect(route('callCenter.dashboard'));
                    } elseif($user->role_id == 3){
                        return redirect(route('restaurant.dashboard'));
                    }

                }else{
                    return back()->withErrors(['password' => 'Invalid password'])->withInput();
                }
            }else{
                return back()->withErrors(['password' => 'Invalid email or password'])->withInput();
            }
        }
        return view('login');
    }

}
