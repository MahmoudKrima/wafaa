<?php

namespace App\Http\Controllers\Front\Home;

use App\Models\Term;
use App\Models\User;
use App\Models\About;
use App\Models\Admin;
use App\Models\Slider;
use App\Models\Contact;
use App\Models\Partner;
use App\Models\Service;
use App\Models\AboutItem;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Front\Contact\StoreContactRequest;

class HomeController extends Controller
{
    public function index()
    {
        $sliders = Slider::where('status', 'active')
            ->orderBy('id')
            ->get();

        $about = About::first();
        $aboutItems = AboutItem::orderBy('id')->get();
        $partners = $this->getActivePartners();
        $services = $this->getActiveServices();
        $testimonials = $this->getActiveTestimonials();
        $statistics = $this->getStatistics();

        return view('front.pages.home.index', compact('sliders', 'about', 'aboutItems', 'partners', 'services', 'testimonials', 'statistics'));
    }


    public function getTotalShippingCompanies()
    {
        return Cache::remember('total_shipping_companies', 3600, function () {
            try {
                $response = Http::withHeaders([
                    'accept' => '*/*',
                    'x-api-key' => 'xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu'
                ])->get('https://ghaya-express-staging-af597af07557.herokuapp.com/api/shipping-companies', [
                    'page' => 0,
                    'pageSize' => 1,
                    'orderColumn' => 'createdAt',
                    'orderDirection' => 'desc'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $data['total'] ?? 0;
                } else {
                    return 0;
                }
            } catch (\Exception $e) {
                return 0;
            }
        });
    }


    public function getStatistics()
    {
        $totalUsers = User::count();
        $totalAdmins = Admin::count();
        $totalShippingCompanies = $this->getTotalShippingCompanies();
        $totalShippments = Service::count();

        return [
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalShippingCompanies' => $totalShippingCompanies,
            'totalShippments' => $totalShippments,
        ];
    }

    private function getActivePartners()
    {
        return Cache::remember('partners', 3600, function () {
            return Partner::where('status', 'active')
                ->orderBy('id')
                ->get();
        });
    }

    private function getActiveServices()
    {
        return Cache::remember('services', 3600, function () {
            return Service::where('status', 'active')
                ->orderBy('id')
                ->get();
        });
    }

    private function getActiveTestimonials()
    {
        return Cache::remember('testimonials', 3600, function () {
            return Testimonial::where('status', 'active')
                ->orderBy('id')
                ->get();
        });
    }

    public function contact(StoreContactRequest $request)
    {
        Contact::create($request->validated());
        return back()
            ->with('Success', __('admin.message_sent_successfully'));
    }

    public function terms()
    {
        $term = Term::select('id', 'term_description')->first();
        return view('front.pages.home.terms', compact('term'));
    }
}
