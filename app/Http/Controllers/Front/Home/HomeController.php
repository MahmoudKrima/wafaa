<?php

namespace App\Http\Controllers\Front\Home;

use App\Services\Front\HomeService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Front\Contact\StoreContactRequest;

class HomeController extends Controller
{
    public function __construct(private HomeService $homeService)
    {
    }

    public function index()
    {
        $sliders = $this->homeService->getSlider();
        $about = $this->homeService->about();
        $aboutItems = $this->homeService->aboutItem();
        $partners = $this->homeService->getActivePartners();
        $services = $this->homeService->getActiveServices();
        $testimonials = $this->homeService->getActiveTestimonials();
        $statistics = $this->homeService->getStatistics();

        return view('front.pages.home.index', compact('sliders', 'about', 'aboutItems', 'partners', 'services', 'testimonials', 'statistics'));
    }

    public function contact(StoreContactRequest $request)
    {
        $this->homeService->contact($request);
        return back()
            ->with('Success', __('admin.message_sent_successfully'));
    }

    public function terms()
    {
        $term = $this->homeService->term();
        return view('front.pages.home.terms', compact('term'));
    }

    public function policy()
    {
        $policy = $this->homeService->policy();
        return view('front.pages.home.policy', compact('policy'));
    }
}
