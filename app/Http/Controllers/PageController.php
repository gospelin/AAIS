<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function newsletter()
    {
        return view('newsletter');
    }

    public function aboutUs()
    {
        return view('about');
    }

    public function admissions()
    {
        return view('admissions');
    }

    public function programs()
    {
        return view('programs');
    }

    public function newsEvents()
    {
        return view('news_events');
    }

    public function gallery()
    {
        return view('gallery');
    }
}
