<?php

namespace App\Http\Controllers\Api\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderLog;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{
    public function index(Request $request){

        $orders = Order::with('restaurant');
        if ($request->has('duration') && $request->get('duration') == 'daily'){
            $orders = $orders->where('created_at', 'LIKE',  '%'.date('Y-m-d').'%');
        }
        $orders = $orders->orderBy('created_at','desc')->get();
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

    public function show(Request $request){
        $order = Order::where('id', $request->id)->with('orderItems', 'orderItems.product')
            ->first();
        if ($order != null){
            return $this->success($order);
        }else{
            return $this->error("Order Not Found");
        }
    }

    public function add(Request $request){

        if ($request->products == null || empty($request->products)){
            return $this->error("Minimum one product required");
        }
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]);

        if ($validator->fails()){
            return $this->error("Validation error", 200, $validator->errors());
        }

        $today = date("Ymd");
        $rand = strtoupper(substr(uniqid(sha1(time())), 0, 4));
        $order_no = $today . $rand;
        $subTotal = $total = $discount = $tax = $deliveryCharge = 0;

        $order = Order::create([
            'order_number' => $order_no,
            'restaurant_id' => $request->restaurant_id,
            'customer_name' => $request->name,
            'customer_phone' => $request->phone,
            'customer_email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'notes' => $request->notes,
            'order_placed_by' => Auth::id(),
            'status_updated_by' => Auth::id(),
        ]);
        if (!empty($request->products)){
            foreach ($request->products as $key => $product){

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product['id'],
                    'price' => $product['price'] ?? 0,
                    'size' => $product['size'],
                    'quantity' => isset($product['quantity']) ? $product['quantity'] : 1,
                ]);
                $subTotal += $product['price'];
            }
        }
        $order->update([
            'sub_total' => $subTotal,
            'tax' => $tax,
            'delivery_charges' => $deliveryCharge,
            'discount' => $discount,
            'total' => ($subTotal + $tax + $deliveryCharge) - $discount,
        ]);

        OrderLog::create([
            'order_id' => $order->id,
            'activity_by_id' => Auth::id(),
            'activity_by_name' => Auth::user()->name,
            'status' => 'Pending',
            'note' => 'Order Created'
        ]);

        return $this->success($order, "Order placed successfully");
    }

    public function edit(Request $request){
        $order = Order::with('orderItems')->find($request->id);
        if ($request->products == null || empty($request->products)){
            return $this->error("Minimum one product required");
        }
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]);

        if ($validator->fails()){
            return $this->error("Validation error", 200, $validator->errors());
        }

        $order->customer_name = $request->name ?? $order->customer_name;
        $order->customer_phone = $request->phone ?? $order->customer_phone;
        $order->customer_email = $request->email ?? $order->customer_email;
        $order->address = $request->address ?? $order->address;
        $order->city = $request->city ?? $order->city;
        $order->state = $request->state ?? $order->state;
        $order->country = $request->country ?? $order->country;
        $order->save();

        $subTotal = $total = $discount = $tax = $deliveryCharge = 0;
        if (!empty($request->products)){
            OrderItem::where('order_id', $request->id)->delete();
            foreach ($request->products as $key => $product){
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product['id'],
                    'price' => $product['price'] ?? 0,
                    'size' => $product['size'],
                    'quantity' => isset($product['quantity']) ? $product['quantity'] : 1,
                ]);
                $subTotal += $product['price'];
            }
        }
        $order->update([
            'sub_total' => $subTotal,
            'tax' => $tax,
            'delivery_charges' => $deliveryCharge,
            'discount' => $discount,
            'total' => ($subTotal + $tax + $deliveryCharge) - $discount,
        ]);
        OrderLog::create([
            'order_id' => $order->id,
            'activity_by_id' => Auth::id(),
            'activity_by_name' => Auth::user()->name,
            'status' => $order->status,
            'note' => 'Order Updated'
        ]);

        return $this->success($order, "Order updated successfully");

    }

    public function destroy(Request $request){
        $content = Order::find($request->id);
        if ($content != null){
            $content->delete();
            OrderLog::create([
                'order_id' => $request->id,
                'activity_by_id' => Auth::id(),
                'activity_by_name' => Auth::user()->name,
                'status' => 'Deleted',
                'note' => 'Order Deleted'
            ]);
            return $this->success([], "Order deleted successfully");
        }
        return $this->error("Order not found");
    }

    public function changeOrderStatus(Request $request)
    {
        $order = Order::where('id', $request->id)->first();
        if ($order != null) {
            $order->update(['status' => $request->status]);
            OrderLog::create([
                'order_id' => $order->id,
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

    public function searchOrder(Request $request){
        $order = Order::where('customer_phone', 'LIKE', '%'.$request->phone.'%')
            ->orderBy('id', 'desc')->first();

        if ($order != null){
            return $this->success($order);
        }else{
            return $this->error("No result found");
        }
    }
}
