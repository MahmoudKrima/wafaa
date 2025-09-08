<?php

namespace App\Http\Controllers\Admin\Reciever;

use App\Http\Controllers\Controller;
use App\Services\Admin\Reciever\RecieverService;
use App\Http\Requests\Admin\Reciever\SearchRecieverRequest;
use App\Models\User;


class RecieverController extends Controller
{
    public function __construct(
        private RecieverService $recieverService,
    ) {
    }

    public function index(User $user)
    {
        if (!$user || $user->created_by != getAdminIdOrCreatedBy()) {
            return redirect()
                ->route('admin.users.index')
                ->with('Error', __('admin.not_found_data'));
        }
        $recievers = $this->recieverService->index($user);
        return view('dashboard.pages.recievers.index', compact('recievers', 'user'));
    }

    public function search(SearchRecieverRequest $request, User $user)
    {
        if (!$user || $user->created_by != getAdminIdOrCreatedBy()) {
            return redirect()
                ->route('admin.users.index')
                ->with('Error', __('admin.not_found_data'));
        }
        $recievers = $this->recieverService->search($request, $user);
        return view('dashboard.pages.recievers.index', compact('recievers', 'user'));
    }
}
