<?php

namespace App\Services\Admin\Contact;

use DB;
use Exception;
use App\Models\Contact;
use App\Filters\EmailFilter;
use App\Mail\ReplyContactMail;
use App\Filters\FullNameFilter;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Mail;
use App\Filters\ActivationStatusFilter;

class ContactService
{
    public function index($request)
    {
        $request->validated();
        return app(Pipeline::class)
            ->send(Contact::query())
            ->through([
                FullNameFilter::class,
                EmailFilter::class,
                ActivationStatusFilter::class
            ])
            ->thenReturn()
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 ELSE 2 END")
            ->orderByDesc('id')
            ->paginate()
            ->withQueryString();
    }

    public function reply($request)
    {
        $data = $request->validated();
        $contact = Contact::where('id', $data['contact'])
            ->where('status', 'pending')
            ->first();
        if (!$contact) {
            return 'not found';
        }
        DB::beginTransaction();
        try {
            if (env('SEND_MAIL', false)) {
                Mail::to($contact->email)
                    ->send(new ReplyContactMail($data['message']));
            }
            $contact->update([
                'status' => 'replied',
            ]);
            DB::commit();
            return 'replied';
        } catch (Exception $e) {
            DB::rollBack();
            return 'server error';
        }
    }

    public function delete($contact)
    {
        $contact->delete();
    }
}
