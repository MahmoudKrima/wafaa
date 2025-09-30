<?php

namespace App\Http\Controllers\User\Reciever;

use App\Models\Reciever;
use App\Http\Controllers\Controller;
use App\Services\User\Reciever\RecieverService;
use App\Http\Requests\User\Reciever\StoreRecieverRequest;
use App\Http\Requests\User\Reciever\SearchRecieverRequest;
use App\Http\Requests\User\Reciever\UpdateRecieverRequest;


class RecieverController extends Controller
{
    public function __construct(
        private RecieverService $recieverService,
    ) {}

    public function index()
    {
        $recievers = $this->recieverService->index();
        return view('user.pages.recievers.index', compact('recievers'));
    }

    public function search(SearchRecieverRequest $request)
    {
        $recievers = $this->recieverService->search($request);
        return view('user.pages.recievers.index', compact('recievers'));
    }

    public function create()
    {
        $shippingCompanies = $this->recieverService->getShippingCompanies();
        return view('user.pages.recievers.create', compact('shippingCompanies'));
    }

    public function store(StoreRecieverRequest $request)
    {
        $this->recieverService->store($request);
        return redirect()
            ->route('user.recievers.index')
            ->with('Success', __('admin.created_successfully'));
    }

    public function edit(Reciever $reciever)
    {
        $shippingCompanies = $this->recieverService->getShippingCompanies();
        $reciever->load('shippingCompanies');
        return view('user.pages.recievers.edit', compact('reciever', 'shippingCompanies'));
    }


    public function update(UpdateRecieverRequest $request, Reciever $reciever)
    {
        $this->recieverService->update($request, $reciever);
        return redirect()
            ->route('user.recievers.index')
            ->with('Success', __('admin.updated_successfully'));
    }

    public function delete(Reciever $reciever)
    {
        $this->recieverService->delete($reciever);
        return redirect()
            ->route('user.recievers.index')
            ->with('Success', __('admin.deleted_successfully'));
    }

    public function getCitiesByCompanyAndCountry($shippingCompanyId)
    {
        $countryId = '65fd1a1c1fdbc094e3369b29';
        $cities = $this->recieverService->getCitiesByCompanyAndCountry($shippingCompanyId, $countryId);
        
        return response()->json($cities);
    }
}
