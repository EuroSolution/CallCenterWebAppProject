<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        $orders = Order::where('restaurant_id', Auth::user()->restaurant_id)
            ->where('created_at', 'LIKE',  '%'.date('Y-m-d').'%')->count();

        $recentOrders = Order::where('restaurant_id', Auth::user()->restaurant_id)
            ->limit(10)->get();

        return $this->success(array('order_count' => $orders, 'recent_order' => $recentOrders));
    }

    public function orders(Request $request){

        $orders = Order::with('restaurant');
        if ($request->has('duration') && $request->get('duration') == 'daily'){
            $orders = $orders->where('created_at', 'LIKE',  '%'.date('Y-m-d').'%');
        }
        $orders = $orders->where('restaurant_id', Auth::user()->restaurant_id)
            ->orderBy('created_at','desc')->get();
        $data = array();
        foreach ($orders as $order){
            $data[] = array(
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'customer_email' => $order->customer_email,
                'total' => $order->total,
                'status' => $order->status,
                'order_date' => date('d-M-Y H:i:s', strtotime($order->created_at)),
            );
        }
        return $this->success($data);
    }

    public function showOrderDetail(Request $request){
        $order = Order::where('id', $request->id)->with('orderItems', 'orderItems.product')
            ->first();
        if ($order != null){
            return $this->success($order);
        }else{
            return $this->error("Order Not Found");
        }

    }

    public function changeOrderStatus(Request $request)
    {
        $order = Order::where('id', $request->id)->first();
        if ($order != null) {
            $order->update(['status' => $request->status]);
            OrderLog::create([
                'order_id' => $request->id,
                'activity_by_id' => Auth::id(),
                'activity_by_name' => Auth::user()->name,
                'status' => $request->status,
                'note' => 'Status Updated'
            ]);
            return $this->success($order);
        } else {
            return $this->error("Order Not Found");
        }
    }
}
