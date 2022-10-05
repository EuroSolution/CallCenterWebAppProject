<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class StaffController extends Controller
{
    public function index(Request $request){
        try {
            if (request()->ajax()) {
                return datatables()->of(User::where('role_id', 2)->orderBy('id', 'desc')->get())
                    ->addIndexColumn()
                    ->addColumn('action', function ($data) {
                        return '<a title="edit" href="edit/' . $data->id . '" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>&nbsp;<button title="Delete" type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
                    })->rawColumns(['action'])->make(true);
            }
        } catch (\Exception $ex) {
            return redirect('/dashboard')->with('error', $ex->getMessage());
        }
        return view('admin.staff.index');
    }

    public function add(Request $request){
        if($request->method()=='POST'){
            $validator = Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'phone' => 'required'
            ]);

            if ($validator->fails()){
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }

            User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' => Hash::make($request->input('password')),
                'role_id' => 2,
                'role_name' => 'Call Center',
                'status' => 1,
            ]);
            return redirect()->back()->with(['success' => 'User Added Successfully']);
        }
        return view('admin.staff.add-staff');
    }

    public function edit(Request $request, $id){
        $content = User::find($id);
        if($request->method() == 'POST'){
            if($request->input('editCheck') && $request->input('editCheck') == 'yes'){
                $validator = Validator::make($request->all(),[
                    'name' => 'required',
                    'email' => 'required|email',
                    'password' => 'required',
                    'phone' => 'required'
                ]);

                $password = $request->input('password');
            }else{
                $validator = Validator::make($request->all(),[
                    'name' => 'required',
                    'email' => 'required|email',
                    'phone' => 'required'
                ]);

                $password = null;
            }
            if ($validator->fails()){
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $content->name = $request->input('name');
            $content->email = $request->input('email');
            $content->phone = $request->input('phone');
            if(isset($password) && $password != ''){
                $content->password = Hash::make($request->input('password'));
            }

            $content->save();

            return redirect()->back()->with(['success' => 'User Updated Successfully']);
        }else{
            return view('admin.staff.add-staff', compact('content'));
        }
    }

    public function destroy($id){
        $staff = User::find($id);
        if ($staff != null){
            $staff->delete();
            echo true;
        }
        echo false;
    }
}
