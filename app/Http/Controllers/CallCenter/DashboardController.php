<?php

namespace App\Http\Controllers\CallCenter;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $restaurants = Restaurant::all();
        return view('call-center.dashboard', compact('restaurants'));
    }
}
