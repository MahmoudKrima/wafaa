<?php

namespace App\Http\Controllers\User\WalletLogs;

use App\Http\Controllers\Controller;
use App\Services\User\WalletLogs\WalletLogsService;
use App\Http\Requests\User\WalletLogs\SearchWalletLogsRequest;
use App\Enum\TransactionTypeEnum;
use App\Enum\WalletLogTypeEnum;

class WalletLogsController extends Controller
{
    public function __construct(private WalletLogsService $walletLogsService) {}

    public function index(SearchWalletLogsRequest $request)
    {
        $logs = $this->walletLogsService->index($request);
        $types = TransactionTypeEnum::cases();
        $trans_types = WalletLogTypeEnum::cases();
        return view('user.pages.wallet_logs.index', compact('logs', 'types', 'trans_types'));
    }
}
