<?php

namespace App\Controllers;

class FooterCont extends BaseController
{
    public function faq()
    {
        return view('faq');
    }

    public function shipping_info()
    {
        return view('shipping_faq');
    }

    public function returns()
    {
        return view('returns');
    }

    public function contact()
    {
        return view('contact_us');
    }

    public function terms()
    {
        return view('terms');
    }

    public function privacy()
    {
        return view('privacy');
    }
}
