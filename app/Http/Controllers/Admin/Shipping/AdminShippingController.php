<?php

namespace App\Http\Controllers\Admin\Shipping;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\Admin\Shipping\AdminShippingService;
use App\Http\Requests\Admin\Shipping\SearchShippingRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;

class AdminShippingController extends Controller
{
    public function __construct(private AdminShippingService $adminShippingService) {}

    public function index(SearchShippingRequest $request, ?User $user = null)
    {
        $page    = max(1, (int) $request->input('page', 1));
        $perPage = (int) $request->input('pageSize', 10);

        $filters = $request->validated();

        if ($request->filled('isCod')) {
            $filters['isCod'] = $request->input('isCod') === 'true' ? 'true' : 'false';
        }

        if ($user) {
            $check = User::where('created_by', getAdminIdOrCreatedBy())
                ->where('id', $user->id)
                ->first();
            if (!$check) {
                return back()->with('Error', __('admin.user_not_found'));
            }
            $users = [(string) $user->id];
            unset($filters['userId']);
        } elseif (!empty($filters['userId'])) {
            $ids   = is_array($filters['userId']) ? $filters['userId'] : [$filters['userId']];
            $users = array_values(array_map('strval', $ids));
            unset($filters['userId']);
        } else {
            $users = User::where('created_by', getAdminIdOrCreatedBy())
                ->pluck('id')->map(fn($id) => (string) $id)->values()->all();
        }

        $hasReceiverFilters = filled($filters['receiverName'] ?? null) || filled($filters['receiverPhone'] ?? null);

        if ($hasReceiverFilters) {
            $filters = array_merge($filters, [
                'page'     => 0,
                'pageSize' => max($perPage, 200),
            ]);
        } else {
            $filters = array_merge($filters, [
                'page'     => $page - 1,
                'pageSize' => $perPage,
            ]);
        }

        $data    = $this->adminShippingService->getUserListShipments($filters, $users);
        $results = collect($data['results'] ?? []);

        $receiverName  = (string) $request->input('receiverName', '');
        $receiverPhone = (string) $request->input('receiverPhone', '');

        if ($receiverName || $receiverPhone) {
            $results = $results->filter(function ($shipment) use ($receiverName, $receiverPhone) {
                $details = data_get($shipment, 'shipmentDetails', []);
                $name    = (string) data_get($details, 'receiverName', '');
                $phone   = (string) data_get($details, 'receiverPhone', '');
                $ok      = true;
                if ($receiverName)  $ok = $ok && (mb_stripos($name, $receiverName) !== false);
                if ($receiverPhone) $ok = $ok && ($phone === $receiverPhone);
                return $ok;
            })->values();

            $total     = $results->count();
            $offset    = ($page - 1) * $perPage;
            $pageItems = $results->slice($offset, $perPage)->values();

            $shipments = new LengthAwarePaginator(
                $pageItems,
                $total,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $apiTotal = (int) (
                data_get($data, 'total') ??
                data_get($data, 'pagination.total') ??
                data_get($data, 'totalCount') ??
                data_get($data, 'count') ??
                0
            );

            if ($apiTotal > 0) {
                $shipments = new LengthAwarePaginator(
                    $results,
                    $apiTotal,
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            } else {
                $shipments = new Paginator(
                    $results,
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'pageName' => 'page']
                );
            }
        }

        $companies    = $this->adminShippingService->getShippingCompanies();
        $allUsers     = User::where('created_by', getAdminIdOrCreatedBy())->get();
        $forcedUserId = $user?->id;
        return view('dashboard.pages.admin_shipping.index', compact(
            'shipments',
            'companies',
            'allUsers',
            'forcedUserId'
        ));
    }

    public function export(SearchShippingRequest $request)
    {
        return $this->adminShippingService->export($request);
    }

    public function show(string $id)
    {
        $data = $this->adminShippingService->show($id);
        return view('dashboard.pages.admin_shipping.show', $data);
    }

    public function delete(string $id, $externalAppId)
    {
        $data = $this->adminShippingService->delete($id, $externalAppId);
        if ($data == 'canceled') {
            return back()
                ->with('Success', __('admin.canceled_successfully'));
        } else {
            return back()
                ->with('Error', __('admin.canceled_failed'));
        }
    }

    public function printWaybills(Request $request)
    {
        $urls = $request->input('pdf_urls', []); 

        // filter empty urls
        $urls = array_filter($urls, function ($url) {
            return !empty($url) && filter_var($url, FILTER_VALIDATE_URL);
        });
        
        if (empty($urls)) {
            return back()->with('Error', ' لا تتوفر بوليصات لهذه الشحنات');
        }

        $localFiles = [];

        foreach ($urls as $key => $url) {
            $pdfContent = Http::get($url)->body();
            $path = storage_path("app/temp_pdf_$key.pdf");
            file_put_contents($path, $pdfContent);
            $localFiles[] = escapeshellarg($path); 
        }

        $outputPath = storage_path('app/merged_labels.pdf');

        // secure the final path
        $outputPathEscaped = escapeshellarg($outputPath);

        //  Ghostscript command
        $cmd = "gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile={$outputPathEscaped} " . implode(" ", $localFiles);

        exec($cmd . " 2>&1", $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputPath)) {
            return response()->json([
                'error' => 'فشل في دمج بوليصات الشحن',
                'cmd' => $cmd,
                'output' => $output,
                'returnCode' => $returnCode
            ], 500);
        }

        return response()->download($outputPath, 'waybills.pdf')->deleteFileAfterSend(true);

    }
}
