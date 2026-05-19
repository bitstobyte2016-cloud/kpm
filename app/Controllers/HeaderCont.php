<?php

namespace App\Controllers;

class HeaderCont extends BaseController
{
    public function search()
    {
        $query = $this->request->getGet('q');

        // Empty search function for now
        // Normally this would query the database based on $query

        // For now, we'll just return a string or you can redirect/show a view later
        return "Search results for: " . esc($query);
    }
    
    
    
    // user register
    public function register(){
        return view('register');
    }
    
    
    //user cart
    public function cart(): string
    {
        return view('cart');
    }
    
    //open all categories page
    public function categories_all(): string
    {
        return view('categories_all');
    }
    
    //open all bands page
    public function bands_all(): string
    {
        return view('bands_all');
    }
    
    //open pre-order items page
    public function preorder(): string
    {
        return view('preorder');
    }

    //open on hand items page
    public function onhand(): string
    {
        return view('onhand');
    }

}
