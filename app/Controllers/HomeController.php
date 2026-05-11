<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index(): string
    {
        return view('store_home');
    }

    public function cart(): string
    {
        return view('cart');
    }

    public function categories(): string
    {
        return view('categories');
    }
}
