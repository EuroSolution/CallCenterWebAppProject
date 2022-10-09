<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        $orders = Order::where('restaurant_id', Auth::user()->restaurant_id)->count();
        return view('restaurant.dashboard', compact('orders'));
    }

    public function orders(Request $request){
        try {
            if (request()->ajax()) {
                return datatables()->of(Order::with('restaurant')->orderBy('created_at','desc')->get())
                    ->addIndexColumn()
                    ->addColumn('customer', function ($data) {
                        return $data->customer_name ?? "Customer";
                    })->addColumn('phone', function ($data) {
                        return $data->customer_phone ?? "--";
                    })->addColumn('total_amount', function ($data) {
                        return ($data->total) ?? '';
                    })->addColumn('order_date', function ($data) {
                        return date('d-M-Y H:i:s', strtotime($data->created_at)) ?? '';
                    })->addColumn('status', function ($data) {
                        if ($data->status == 'Pending') {
                            return '<span class="badge badge-secondary">Pending</span>';
                        } elseif ($data->status == 'Cancelled') {
                            return '<span class="badge badge-danger">Cancelled</span>';
                        } elseif ($data->status == 'Processing') {
                            return '<span class="badge badge-primary">Processing</span>';
                        } elseif ($data->status == 'Delivered') {
                            return '<span class="badge badge-info">Delivered</span>';
                        } elseif ($data->status == 'Completed') {
                            return '<span class="badge badge-success">Completed</span>';
                        }else{
                            return "";
                        }
                    })
                    ->addColumn('action', function ($data) {
                        return '<a title="View" href="order/show/' . $data->id . '" class="btn btn-dark btn-sm"><i class="fas fa-eye"></i></a>&nbsp;';
                    })->rawColumns(['customer', 'phone', 'status', 'total_amount', 'order_date', 'action'])->make(true);
            }
        } catch (\Exception $ex) {
            return redirect(route('restaurant.dashboard'))->with('error', 'SomeThing Went Wrong baby');
        }
        return view('restaurant.order.index');
    }

    public function showOrderDetail($id){
        $order = Order::where('id', $id)->with('orderItems', 'orderItems.product')
            ->firstOrFail();
        return view('restaurant.order.show', compact('order'));
    }

    public function changeOrderStatus(Request $request, $id)
    {
        $order = Order::where('id', $id)->first();
        if ($order != null) {
            $order->update(['status' => $request->val]);
            OrderLog::create([
                'order_id' => $id,
                'activity_by_id' => Auth::id(),
                'activity_by_name' => Auth::user()->name,
                'status' => $request->val,
                'note' => 'Status Updated'
            ]);
            return true;
        } else {
            return false;
        }
    }
}
