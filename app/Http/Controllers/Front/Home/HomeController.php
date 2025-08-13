<?php

namespace App\Http\Controllers\Front\Home;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\About;
use App\Models\AboutItem;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::where('status', 'active')
                         ->orderBy('id')
                         ->get();
        
        $about = About::first();
        $aboutItems = AboutItem::orderBy('id')->get();
        
        return view('front.pages.home.index', compact('sliders', 'about', 'aboutItems'));
    }
}
