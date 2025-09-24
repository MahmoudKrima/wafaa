<?php

namespace App\Http\Controllers\User\Sender;

use App\Models\Sender;
use App\Http\Controllers\Controller;
use App\Services\User\Sender\SenderService;
use App\Http\Requests\User\Sender\StoreSenderRequest;
use App\Http\Requests\User\Sender\SearchSenderRequest;
use App\Http\Requests\User\Sender\UpdateSenderRequest;


class SenderController extends Controller
{
    public function __construct(
        private SenderService $senderService,
    ) {}

    public function index()
    {
        $senders = $this->senderService->index();
        return view('user.pages.senders.index', compact('senders'));
    }

    public function search(SearchSenderRequest $request)
    {
        $senders = $this->senderService->search($request);
        return view('user.pages.senders.index', compact('senders'));
    }

    public function create()
    {
        return view('user.pages.senders.create');
    }

    public function store(StoreSenderRequest $request)
    {
        $this->senderService->store($request);
        return redirect()
            ->route('user.senders.index')
            ->with('Success', __('admin.created_successfully'));
    }

    public function edit(Sender $sender)
    {
        return view('user.pages.senders.edit', compact('sender'));
    }


    public function update(UpdateSenderRequest $request, Sender $sender)
    {
        $this->senderService->update($request, $sender);
        return redirect()
            ->route('user.senders.index')
            ->with('Success', __('admin.updated_successfully'));
    }

    public function delete(Sender $sender)
    {
        $this->senderService->delete($sender);
        return redirect()
            ->route('user.senders.index')
            ->with('Success', __('admin.deleted_successfully'));
    }

    public function getSenders()
    {
        $senders = $this->senderService->getSenders();
        return response()->json($senders);
    }
}
