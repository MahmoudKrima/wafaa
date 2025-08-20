<?php

namespace App\Services\Admin\UserShippingPrice;

use App\Models\UserShippingPrice;
use App\Traits\ImageTrait;
use App\Traits\TranslateTrait;

class UserShippingPriceService
{
    use ImageTrait, TranslateTrait;

    public function getAll($user)
    {
        return UserShippingPrice::where('user_id', $user->id)
            ->whereRelation('user', 'created_by', getAdminIdOrCreatedBy())
            ->withAllRelations()
            ->orderBy('created_at', 'desc')
            ->paginate();
    }

    public function storeUserShippingPrice($request, $user)
    {
        $data = $request->validated();
        $data['user_id'] = $user->id;
        $data['company_name'] = $this->translate($data['company_name_ar'], $data['company_name_en']);
        return UserShippingPrice::create($data);
    }

    public function updateUserShippingPrice($userShippingPrice, $data)
    {
        $data['company_name'] = $this->translate($data['company_name_ar'], $data['company_name_en']);
        $userShippingPrice->update($data);
        return $userShippingPrice;
    }

    public function updateUserShippingPriceStatus($userShippingPrice, $status)
    {
        $userShippingPrice->update(['is_active' => $status]);
        return $userShippingPrice;
    }

    public function deleteUserShippingPrice($userShippingPrice)
    {
        return $userShippingPrice->delete();
    }

    public function findById($id)
    {
        return UserShippingPrice::findOrFail($id);
    }
}
