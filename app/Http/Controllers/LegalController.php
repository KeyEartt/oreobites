<?php

namespace App\Http\Controllers;

class LegalController extends Controller
{
    public function terms()
    {
        return view('legal.terms', [
            'lastUpdated' => 'September 22, 2026',
        ]);
    }

    public function privacy()
    {
        return view('legal.privacy', [
            'lastUpdated' => 'September 22, 2026',
        ]);
    }
}