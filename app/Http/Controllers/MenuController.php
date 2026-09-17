<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;

class MenuController extends Controller
{
    public function index(SupabaseService $supabase)
    {
        $products = $supabase->fetchProducts();
        return view('menu', compact('products'));
    }
}