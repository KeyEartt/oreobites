<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;

class HomeController extends Controller
{
    public function index(SupabaseService $supabase)
    {
        $products = $supabase->fetchProducts();
        $featured = array_slice($products, 0, 3);

        return view('home', compact('featured'));
    }
}