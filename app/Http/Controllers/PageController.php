<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home');
    }

    public function shop()
    {
        return view('pages.shop');
    }

    public function product_detail()
    {
        return view('pages.product_detail');
       
    }

    public function about_us()
    {
        return view('pages.about_us');
       
    }
}
