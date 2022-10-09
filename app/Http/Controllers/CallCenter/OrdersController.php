<?php

namespace App\Http\Controllers\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderLog;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    public function index(Request $request){
        try {
            if (request()->ajax()) {
                return datatables()->of(Order::with('restaurant')->orderBy('created_at','desc')->get())
                    ->addIndexColumn()
                    ->addColumn('restaurant', function ($data) {
                        return $data->restaurant->name ?? $data->restaurant_id;
                    })->addColumn('customer', function ($data) {
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
                        return '<a title="View" href="order/show/' . $data->id . '" class="btn btn-dark btn-sm"><i class="fas fa-eye"></i></a>&nbsp;
                                <a title="Edit" href="order/edit/' . $data->id . '" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>&nbsp;
                                <button title="Delete" type="button" name="delete" id="' . $data->id . '" class="delete btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i></button>';
                    })->rawColumns(['restaurant', 'customer', 'phone', 'status', 'total_amount', 'order_date', 'action'])->make(true);
            }
        } catch (\Exception $ex) {
            return redirect(route('callCenter.dashboard'))->with('error', 'SomeThing Went Wrong baby');
        }
        return view('call-center.order.index');
    }

    public function show($id){
        $order = Order::where('id', $id)->with('orderItems', 'orderItems.product')
            ->firstOrFail();
        return view('call-center.order.show', compact('order'));
    }

    public function add(Request $request){
        if ($request->method() == 'POST'){
            $this->validate($request, [
                'restaurant' => 'required',
                'name' => 'required',
                'phone' => 'required',
                'address' => 'required',
            ]);

            $today = date("Ymd");
            $rand = strtoupper(substr(uniqid(sha1(time())), 0, 4));
            $order_no = $today . $rand;
            $subTotal = $total = $discount = $tax = $deliveryCharge = 0;

            $order = Order::create([
                'order_number' => $order_no,
                'restaurant_id' => $request->input('restaurant'),
                'customer_name' => $request->input('name'),
                'customer_phone' => $request->input('phone'),
                'customer_email' => $request->input('email'),
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'country' => $request->input('country'),
                'notes' => $request->input('notes'),
                'order_placed_by' => Auth::id(),
                'status_updated_by' => Auth::id(),
            ]);
            if (!empty($request->input('products'))){
                foreach ($request->input('products') as $key => $product){
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product,
                        'price' => $request->input('prices')[$key] ?? 0,
                        'size' => $request->input('sizes')[$key],
                        'quantity' => $request->input('quantities')[$key] ?? 1,
                    ]);
                    $subTotal += $request->input('prices')[$key];
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

            return redirect(route('callCenter.showOrder', $order->id))->with('success', 'Order Created Successfully');
        }
        $restaurants = Restaurant::where('status', 1)->get();
        $products = array();
        if ($request->get('restaurant_id') != null){
            $products = Product::where('restaurant_id', $request->get('restaurant_id'))->get();
        }

        return view('call-center.order.create', compact('restaurants', 'products'));
    }

    public function edit(Request $request, $id){
        $order = Order::with('orderItems')->findOrFail($id);
        if ($request->method() == 'POST'){
            $this->validate($request, [
                'restaurant' => 'required',
                'name' => 'required',
                'phone' => 'required',
                'address' => 'required',
            ]);

            $order->customer_name = $request->input('name');
            $order->customer_phone = $request->input('phone');
            $order->customer_email = $request->input('email');
            $order->address = $request->input('address');
            $order->city = $request->input('city');
            $order->state = $request->input('state');
            $order->country = $request->input('country');
            $order->save();

            $subTotal = $total = $discount = $tax = $deliveryCharge = 0;
            if (!empty($request->input('products'))){
                OrderItem::where('order_id', $id)->delete();
                foreach ($request->input('products') as $key => $product){
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product,
                        'price' => $request->input('prices')[$key] ?? 0,
                        'size' => $request->input('sizes')[$key],
                        'quantity' => $request->input('quantities')[$key] ?? 1,
                    ]);
                    $subTotal += $request->input('prices')[$key];
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

            return redirect()->back()->with('success', 'Order Updated Successfully');
        }
        $restaurants = Restaurant::where('status', 1)->get();
        $products = Product::where('restaurant_id', $order->restaurant_id)->get();
        return view('call-center.order.edit', compact('order', 'restaurants', 'products'));
    }

    public function destroy($id){
        $content = Order::find($id);
        if ($content != null){
            $content->delete();
            OrderLog::create([
                'order_id' => $id,
                'activity_by_id' => Auth::id(),
                'activity_by_name' => Auth::user()->name,
                'status' => 'Deleted',
                'note' => 'Order Deleted'
            ]);
            return true;
        }
        return false;
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
