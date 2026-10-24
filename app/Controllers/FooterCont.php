<?php

namespace App\Controllers;

class FooterCont extends BaseController
{
    public function faq()
    {
        return view('faq',['page_title'=>'FAQ | K-Pop Merch']);
    }

    public function shipping_info()
    {
        return view('shipping_faq',['page_title'=>'Shipping FAQ | K-Pop Merch']);
    }

    public function returns()
    {
        return view('returns',['page_title'=>'Return Policy | K-Pop Merch']);
    }

    public function terms()
    {
        return view('terms',['page_title'=>'Terms & Conditions | K-Pop Merch']);
    }

    public function privacy()
    {
        return view('privacy',['page_title'=>'Privacy Policy | K-Pop Merch']);
    }
}
