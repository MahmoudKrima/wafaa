<?php

namespace App\Services\Admin\Bank;

use App\Traits\ImageTrait;
use App\Traits\TranslateTrait;
use App\Filters\NameJsonFilter;
use App\Filters\IbanNumberFilter;
use Illuminate\Pipeline\Pipeline;
use App\Filters\AccountOwnerFilter;
use App\Filters\AccountNumberFilter;
use App\Filters\ActivationStatusFilter;
use Illuminate\Support\Facades\Storage;
use App\Models\Banks;

class BankService
{
    use ImageTrait, TranslateTrait;

    function getAll()
    {
        return Banks::where('admin_id', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate();
    }

    function searchBank($request)
    {
        $request->validated();
        return app(Pipeline::class)
            ->send(Banks::query())
            ->through([
                NameJsonFilter::class,
                ActivationStatusFilter::class,
                AccountOwnerFilter::class,
                AccountNumberFilter::class,
                IbanNumberFilter::class
            ])
            ->thenReturn()
            ->where('admin_id', getAdminIdOrCreatedBy())
            ->orderBy('id', 'desc')
            ->paginate()
            ->withQueryString();
    }


    function storeBank($request)
    {
        $data = $request->validated();
        $data['name'] = $this->translate($data['name_ar'], $data['name_en']);
        $data['account_owner'] = $this->translate($data['account_owner_ar'], $data['account_owner_en']);
        $data['image'] = ImageTrait::uploadImage($request->file('image'), 'bank');
        $data['created_by'] = auth('admin')->id();
        $data['admin_id'] = getAdminIdOrCreatedBy();
        Banks::create($data);
    }

    function updateBank($request, $bank)
    {
        $data = $request->validated();
        $data['name'] = $this->translate($data['name_ar'], $data['name_en']);
        $data['account_owner'] = $this->translate($data['account_owner_ar'], $data['account_owner_en']);
        $data['image'] = ImageTrait::updateImage($bank->image, 'bank', 'image');
        $bank->update($data);
    }

    function updateBankStatus($bank)
    {
        if ($bank->status->value == 'active') {
            $bank->update([
                'status' => 'deactive',
            ]);
        } else {
            $bank->update([
                'status' => 'active',
            ]);
        }
    }

    function deleteBank($bank)
    {
        Storage::disk('public')->delete($bank->image);
        $bank->delete();
    }
}
