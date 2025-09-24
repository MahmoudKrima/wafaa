<?php

namespace App\Http\Controllers\Admin\Sender;

use App\Http\Controllers\Controller;
use App\Services\Admin\Sender\SenderService;
use App\Http\Requests\Admin\Sender\SearchSenderRequest;
use App\Models\User;


class SenderController extends Controller
{
    public function __construct(
        private SenderService $senderService,
    ) {}

    public function index(User $user)
    {
        if (!$user || $user->created_by != getAdminIdOrCreatedBy()) {
            return redirect()
                ->route('admin.users.index')
                ->with('Error', __('admin.not_found_data'));
        }
        $senders = $this->senderService->index($user);
        return view('dashboard.pages.senders.index', compact('senders', 'user'));
    }

    public function search(SearchSenderRequest $request, User $user)
    {
        if (!$user || $user->created_by != getAdminIdOrCreatedBy()) {
            return redirect()
                ->route('admin.users.index')
                ->with('Error', __('admin.not_found_data'));
        }
        $senders = $this->senderService->search($request, $user);
        return view('dashboard.pages.senders.index', compact('senders', 'user'));
    }
}
