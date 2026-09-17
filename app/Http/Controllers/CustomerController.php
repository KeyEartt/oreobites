<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Support\Facades\Session;

class CustomerController extends Controller
{
    public function myOrders(SupabaseService $supabase)
    {
        $user = Session::get('auth_user');
        $orders = $supabase->findOrdersByEmail($user['email']);
        return view('my-orders', compact('orders'));
    }
}