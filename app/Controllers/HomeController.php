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

    public function bands_all(): string
    {
        return view('bands_all');
    }

    public function categories_all(): string
    {
        return view('categories_all');
    }

    public function preorder(): string
    {
        return view('preorder');
    }

    public function onhand(): string
    {
        return view('onhand');
    }
}
