<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class NewTransactionMail extends Mailable
{
    use Queueable, SerializesModels;

    public Transaction $transaction;
    public ?string $adminName;

    /**
     * @param  Transaction  $transaction
     * @param  string|null  $adminName 
     */
    public function __construct(Transaction $transaction, ?string $adminName = null)
    {
        $this->transaction = $transaction;
        $this->adminName = $adminName;
    }

    public function build()
    {
        return $this->subject(__('admin.new_transaction_mail', ['code' => $this->transaction->code]))
            ->view('dashboard.mail.new_transaction_mail')
            ->with([
                'transaction' => $this->transaction,
                'adminName' => $this->adminName,
            ]); 
    }
}
