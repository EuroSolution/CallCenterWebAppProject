<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    public function index(){
        $data = User::where('role_id', 2)->orderBy('id', 'desc')->get();
        return $this->success($data);
    }

    public function add(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'phone' => 'required'
        ]);

        if ($validator->fails()){
            return $this->error('Validation Error', $validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => 2,
            'role_name' => 'Call Center',
            'status' => 1,
        ]);
        return $this->success($user,'User Added Successfully');
    }

    public function edit(Request $request){
        $id = $request->id;
        $content = User::find($id);
        if($content != null){
            $password = null;
            if($request->password_edit && $request->password_edit == 1){
                $password = $request->password;
            }

            $content->name = $request->name ?? $content->name;
            $content->email = $request->email ?? $content->email;
            $content->phone = $request->phone ?? $content->phone;
            if(isset($password) && $password != '' && $password != null){
                $content->password = Hash::make($password);
            }

            $content->save();
            return $this->success($content,'User Updated Successfully');
        }else{
            return $this->error('User Not Found');
        }
    }

    public function destroy(Request $request){
        $id = $request->id;
        $staff = User::find($id);
        if ($staff != null){
            $staff->delete();
            return $this->success([],'User Deleted Successfully');
        }
        return $this->error('User Not Found');
    }
}
