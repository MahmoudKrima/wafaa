<?php

namespace App\Http\Controllers\Admin\Contact;

use App\Models\Contact;
use App\Enum\ContactStatusEnum;
use App\Http\Controllers\Controller;
use App\Services\Admin\Contact\ContactService;
use App\Http\Requests\Admin\Contact\ReplyContactRequest;
use App\Http\Requests\Admin\Contact\SearchContactRequest;

class ContactController extends Controller
{
    public function __construct(private ContactService $contactService)
    {
    }

    public function index(SearchContactRequest $request)
    {
        $contacts = $this->contactService->index($request);
        $status = ContactStatusEnum::cases();
        return view("dashboard.pages.contact.index", compact('contacts', 'status'));
    }

    public function reply(ReplyContactRequest $request)
    {
        $response = $this->contactService->reply($request);
        if ($response == 'not found') {
            return back()
                ->with('Error', __('admin.not_found_data'));
        } elseif ($response == 'server error') {
            return back()
                ->with('Error', __('admin.server_error'));
        } else {
            return back()
                ->with('Success', __('admin.message_sent_successfully'));
        }
    }

    public function delete(Contact $contact)
    {
        $this->contactService->delete($contact);
        return back()
            ->with('Success', __('admin.deleted_successfully'));
    }
}
