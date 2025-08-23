<?php

namespace App\Services\Admin\Partner;

use App\Models\Partner;
use App\Traits\ImageTrait;

class PartnerService
{
    use ImageTrait;
    public function index()
    {
        return Partner::orderBy('id', 'desc')
            ->paginate();
    }

    public function store($request)
    {
        $data = $request->validated();
        $data['image'] = $this->uploadImage($request->file('image'), 'partners');
        Partner::create($data);
    }

    public function update($request, $partner)
    {
        $data = $request->validated();
        $data['image'] = $this->updateImage($partner->image, 'partners', 'image');
        $partner->update($data);
    }

    public function updateStatus($partner)
    {
        if ($partner->status->value == 'active') {
            $partner->update([
                'status' => 'deactive',
            ]);
        } else {
            $partner->update([
                'status' => 'active',
            ]);
        }
    }

    public function delete($partner)
    {
        $this->deleteFile($partner->image);
        $partner->delete();
    }
}
