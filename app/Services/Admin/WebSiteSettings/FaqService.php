<?php

namespace App\Services\Admin\WebSiteSettings;

use App\Models\Faq;

class FaqService
{
    public function index()
    {
        return Faq::orderBy('id', 'desc')
            ->paginate();
    }

    public function store($request)
    {
        $data = $request->validated();
        Faq::create($data);
    }

    public function update($request, $faq)
    {
        $data = $request->validated();
        $faq->update($data);
    }

    public function updateStatus($faq)
    {
        if ($faq->status->value == 'active') {
            $faq->update([
                'status' => 'deactive',
            ]);
        } else {
            $faq->update([
                'status' => 'active',
            ]);
        }
    }

    public function delete($faq)
    {
        $faq->delete();
    }
}
