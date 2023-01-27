<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email', //|regex:/(0)[0-9]{10}/
            'password' => 'required'
        ]);
        if ($validator->fails()){
            return $this->error('Validation Error', 200, [], $validator->errors());
        }

        $user = User::where('email', $request->email)->first();
        if ($user != null){
            if (Hash::check($request->password, $user->password)) {
                if ($user->status == 1){
                    Auth::login($user);
                }else{
                    return $this->error("Your Account is InActive", 200, ['resend_otp' => true]);
                }
            }else{
                return $this->error("Invalid Password");
            }
        }else{
            return $this->error("Your Account is not exists. Please Signup");
        }

        $user->api_token =  auth()->user()->createToken('API Token')->plainTextToken;
        $user->save();
        return $this->success($user);
    }
}
