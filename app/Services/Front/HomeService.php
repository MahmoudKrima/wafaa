<?php

namespace App\Services\Front;

use App\Models\Faq;
use App\Models\Term;
use App\Models\User;
use App\Models\About;
use App\Models\Admin;
use App\Models\Slider;
use App\Models\Contact;
use App\Models\Partner;
use App\Models\Service;
use App\Models\Reciever;
use App\Models\AboutItem;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HomeService
{
    public function getTotalShippingCompanies()
    {
        return Cache::remember('total_shipping_companies', 3600, function () {
            try {
                $response = Http::withHeaders([
                    'accept' => '*/*',
                    'x-api-key' => config('services.ghaya.key'),
                ])->get($this->ghayaUrl('shipping-companies'), [
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

    private function ghayaRequest()
    {
        return Http::withHeaders([
            'accept' => '*/*',
            'x-api-key' => config('services.ghaya.key'),
        ]);
    }

    private function ghayaUrl(string $endpoint): string
    {
        return rtrim(config('services.ghaya.base_url'), '/') . '/' . ltrim($endpoint, '/');
    }

    private function getTotalShipments(): int
    {
        $userIds = User::pluck('id')
            ->map(fn($id) => (string) $id)
            ->values()
            ->all();

        if (empty($userIds)) {
            return 0;
        }

        $base = [
            'page' => 0,
            'pageSize' => 1,
            'orderColumn' => 'createdAt',
            'orderDirection' => 'desc',
        ];

        $total = 0;
        foreach (array_chunk($userIds, 150) as $chunk) {
            $parts = [];
            foreach ($base as $k => $v) {
                $parts[] = urlencode($k) . '=' . urlencode((string) $v);
            }
            $url = $this->ghayaUrl('shipments') . '?' . implode('&', $parts);

            try {
                $res = $this->ghayaRequest()->get($url);
                $json = $res->json();
                $total += (int) data_get($json, 'total', 0);
            } catch (\Throwable $e) {
            }
        }

        return $total;
    }


    public function getStatistics()
    {
        $totalUsers = User::count() + Reciever::count();
        $totalAdmins = Admin::count();
        $totalShippingCompanies = $this->getTotalShippingCompanies();
        $totalShippments = $this->getTotalShipments();

        return [
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalShippingCompanies' => $totalShippingCompanies,
            'totalShippments' => $totalShippments,
        ];
    }

    public function getActivePartners()
    {
        return Cache::remember('partners', 3600, function () {
            return Partner::where('status', 'active')
                ->orderBy('id')
                ->get();
        });
    }

    public function getActiveServices()
    {
        return Cache::remember('services', 3600, function () {
            return Service::where('status', 'active')
                ->orderBy('id')
                ->get();
        });
    }

    public function getActiveTestimonials()
    {
        return Cache::remember('testimonials', 3600, function () {
            return Testimonial::where('status', 'active')
                ->orderBy('id')
                ->get();
        });
    }

    public function getSlider()
    {
        return Slider::where('status', 'active')
            ->orderBy('id')
            ->get();
    }

    public function aboutItem()
    {
        return AboutItem::orderBy('id')
            ->get();
    }

    public function about()
    {
        return About::first();
    }

    public function contact($request)
    {
        $data = $request->validated();
        Contact::create($data);
    }

    public function term()
    {
        return Term::select('id', 'term_description', 'updated_at')
            ->first();
    }

    public function policy()
    {
        return Term::select('id', 'policy_description', 'updated_at')
            ->first();
    }

    public function getFaqs()
    {
        return Cache::remember('faqs', 3600, function () {
            return Faq::where('status', 'active')
                ->orderBy('id')
                ->get();
        });
    }
}
