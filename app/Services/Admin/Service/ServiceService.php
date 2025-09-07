<?php

namespace App\Services\Admin\Service;

use App\Models\Service;
use App\Traits\ImageTrait;
use App\Traits\TranslateTrait;

class ServiceService
{
    use ImageTrait,TranslateTrait;

    public function index()
    {
        return Service::orderBy('id', 'desc')
            ->paginate();
    }

    public function store($request)
    {
        $data = $request->validated();
        $data['title'] = $this->translate($data['title'], $data['title']);
        $data['description'] = $this->translate($data['description'], $data['description']);
        $data['image'] = $this->uploadImage($request->file('image'), 'services');
        Service::create($data);
    }

    public function update($request, $service)
    {
        $data = $request->validated();
        $data['title'] = $this->translate($data['title'], $data['title']);
        $data['description'] = $this->translate($data['description'], $data['description']);
        $data['image'] = $this->updateImage($service->image, 'services', 'image');
        $service->update($data);
    }

    public function updateStatus($service)
    {
        if ($service->status->value == 'active') {
            $service->update([
                'status' => 'deactive',
            ]);
        } else {
            $service->update([
                'status' => 'active',
            ]);
        }
    }

    public function delete($service)
    {
        $this->deleteFile($service->image);
        $service->delete();
    }
}
