<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Transaction;

class TransactionAlertInsideComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $transactionsInsideCount = Transaction::where('status','pending')
            ->whereHas('user',function($query){
                $query->where('created_by',getAdminIdOrCreatedBy());
            })
            ->count();
        return view('components.transaction-alert-inside-component', compact('transactionsInsideCount'));
    }
}
