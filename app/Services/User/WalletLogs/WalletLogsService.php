<?php

namespace App\Services\User\WalletLogs;

use App\Models\WalletLog;
use Illuminate\Pipeline\Pipeline;
use App\Filters\TransActionFilter;
use App\Filters\WalletLogTypeFilter;


class WalletLogsService
{


    public function index($request)
    {
        $request->validated();
        $logs = app(Pipeline::class)
            ->send(WalletLog::query())
            ->through([
                WalletLogTypeFilter::class,
                TransActionFilter::class
            ])
            ->thenReturn()
            ->where('user_id', auth()->id())
            ->withAllRelations()
            ->orderBy('id')
            ->paginate()
            ->withQueryString();
        return $logs;
    }
}
