<?php

namespace App\Services\Admin\Testimonial;

use App\Models\Testimonial;
use App\Traits\TranslateTrait;

class TestimonialService
{
    use TranslateTrait;

    public function index()
    {
        return Testimonial::orderBy('id', 'desc')
            ->paginate();
    }

    public function store($request)
    {
        $data = $request->validated();
        $data['name'] = $this->translate($data['name_ar'], $data['name_en']);
        $data['job_title'] = $this->translate($data['job_title_ar'], $data['job_title_en']);
        $data['review'] = $this->translate($data['review_ar'], $data['review_en']);
        Testimonial::create($data);
    }

    public function update($request, $testimonial)
    {
        $data = $request->validated();
        $data['name'] = $this->translate($data['name_ar'], $data['name_en']);
        $data['job_title'] = $this->translate($data['job_title_ar'], $data['job_title_en']);
        $data['review'] = $this->translate($data['review_ar'], $data['review_en']);
        $testimonial->update($data);
    }

    public function updateStatus($testimonial)
    {
        if ($testimonial->status->value == 'active') {
            $testimonial->update([
                'status' => 'deactive',
            ]);
        } else {
            $testimonial->update([
                'status' => 'active',
            ]);
        }
    }

    public function delete($testimonial)
    {
        $testimonial->delete();
    }
}
