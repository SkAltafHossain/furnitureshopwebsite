<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('website.pages.about');
    }
    
    public function terms()
    {
        return view('website.pages.terms');
    }
    
    public function privacy()
    {
        return view('website.pages.privacy');
    }
}
