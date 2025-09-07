<?php

namespace App\Services\Admin\WebSiteSettings;

use App\Models\WhyChooseUs;
use App\Traits\ImageTrait;

class WhyChooseUsService
{
    use ImageTrait;

    public function index()
    {
        return WhyChooseUs::first();
    }

    public function update($request)
    {
        $data = $request->validated();
        $whyChooseUs = WhyChooseUs::first();
        $data['image'] = $this->updateImage($whyChooseUs->image, 'why_choose_us', 'image');
        $whyChooseUs->update($data);
        return $whyChooseUs;
    }
}
