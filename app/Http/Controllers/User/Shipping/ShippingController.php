<?php

namespace App\Http\Controllers\User\Shipping;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User\Shipping\ShippingService;
use App\Http\Requests\User\Shipping\StoreShippingRequest;
use App\Http\Requests\User\Shipping\SearchShippingRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

use iio\libmergepdf\Merger;

class ShippingController extends Controller
{
    public function __construct(private ShippingService $shippingService) {}

    public function index(SearchShippingRequest $request)
    {
        $page    = max(1, (int) $request->input('page', 1));
        $perPage = max(1, (int) $request->input('pageSize', 30));
        $filters = $request->validated();
        foreach ($filters as $k => $v) {
            if (is_string($v)) {
                $filters[$k] = trim($v);
            }
        }

        $isCodRaw = $request->query('isCod', null);
        if ($request->filled('isCod')) {
            $isCodBool = filter_var($isCodRaw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isCodBool === true) {
                $filters['isCod'] = 'true';
            } elseif ($isCodBool === false) {
                $filters['isCod'] = 'false';
            } else {
                unset($filters['isCod']);
            }
        } else {
            unset($filters['isCod']);
        }

        foreach (['shippingCompanyId', 'method', 'type', 'status', 'search', 'receiverName', 'receiverPhone', 'dateFrom', 'dateTo'] as $key) {
            if (array_key_exists($key, $filters) && ($filters[$key] === '' || $filters[$key] === null)) {
                unset($filters[$key]);
            }
        }

        $hasReceiverFilters = filled($filters['receiverName'] ?? null) || filled($filters['receiverPhone'] ?? null);

        if ($hasReceiverFilters) {
            $filters['page']     = 0;
            $filters['pageSize'] = max($perPage, 200);
        } else {
            $filters['page']     = $page - 1;
            $filters['pageSize'] = $perPage;
        }

        $filters['orderColumn']    = $filters['orderColumn']    ?? 'createdAt';
        $filters['orderDirection'] = $filters['orderDirection'] ?? 'desc';
        $data = $this->shippingService->getUserListShipments($filters);
        if ($hasReceiverFilters) {
            $results = collect($data['results'] ?? []);

            $receiverName  = $filters['receiverName']  ?? null;
            $receiverPhone = $filters['receiverPhone'] ?? null;

            if ($receiverName || $receiverPhone) {
                $results = $results->filter(function ($shipment) use ($receiverName, $receiverPhone) {
                    $details = data_get($shipment, 'shipmentDetails', []);
                    $name      = (string) data_get($details, 'receiverName', '');
                    $phoneA    = (string) data_get($details, 'receiverPhone', '');
                    $phoneB    = (string) data_get($details, 'receiverPhone1', '');
                    $targetNum = $receiverPhone ? preg_replace('/\D+/', '', $receiverPhone) : null;
                    $numA      = preg_replace('/\D+/', '', $phoneA);
                    $numB      = preg_replace('/\D+/', '', $phoneB);

                    $ok = true;

                    if ($receiverName) {
                        $ok = $ok && (mb_stripos($name, $receiverName) !== false);
                    }

                    if ($receiverPhone) {
                        $ok = $ok && (
                            ($targetNum !== '') &&
                            (
                                Str::contains($numA, $targetNum) ||
                                Str::contains($numB, $targetNum)
                            )
                        );
                    }

                    return $ok;
                })->values();
            }

            $total     = $results->count();
            $offset    = ($page - 1) * $perPage;
            $pageItems = $results->slice($offset, $perPage)->values();
        } else {
            $pageItems = collect($data['results'] ?? []);
            $total = (int) (
                $data['total']
                ?? $data['count']
                ?? data_get($data, 'pagination.total', 0)
            );
        }

        $shipments = new LengthAwarePaginator(
            $pageItems,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $companies = $this->shippingService->getShippingCompanies();
        return view('user.pages.shippings.index', compact('shipments', 'companies'));
    }


    public function downloadWaybill(Request $request)
    {
        $request->validate([
            'label_url' => 'required',
            'tracking_number' => 'required|max:255',
        ]);

        $fileUrl = $request->query('label_url');
        $tracking_number = $request->query('tracking_number');

        // نحاول نحمل الملف من الرابط
        try {
            $response = Http::get($fileUrl);

            if ($response->failed()) {
                return abort(404, 'تعذر تحميل الملف من الرابط المحدد.');
            }

            $contentType = $response->header('Content-Type', 'application/octet-stream');

            $fileName = $tracking_number . '.' . self::getExtensionFromMime($contentType);

            return response($response->body(), 200)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');
        } catch (\Exception $e) {
            return abort(500, 'حدث خطأ أثناء تحميل الملف.');
        }
    }


    private static function getExtensionFromMime($mime)
    {
        $map = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/zip' => 'zip',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        ];

        return $map[$mime] ?? 'file';
    }





    public function export(SearchShippingRequest $request)
    {
        return $this->shippingService->export($request);
    }

    public function create()
    {
        return view('user.pages.shippings.create');
    }

    public function store(StoreShippingRequest $request)
    {
        return $this->shippingService->store($request);
    }

    public function show(string $id)
    {
        $data = $this->shippingService->show($id);
        return view('user.pages.shippings.show', $data);
    }

    public function delete(string $id)
    {
        $data = $this->shippingService->delete($id);
        if ($data == 'canceled') {
            return back()
                ->with('Success', __('admin.canceled_successfully'));
        } else {
            return back()
                ->with('Error', __('admin.canceled_failed'));
        }
    }

    public function receivers($shippingCompanyId)
    {
        $recievers = $this->shippingService->receivers($shippingCompanyId);
        return response()->json($recievers);
    }

    public function shippingCompanies()
    {
        $companies = $this->shippingService->getUserShippingCompanies();
        return response()->json($companies);
    }

    public function getStates()
    {
        $states = $this->shippingService->getStates();
        return response()->json($states);
    }

    public function getCities()
    {
        $cities = $this->shippingService->getCities();
        return response()->json($cities);
    }

    public function getCitiesByState(Request $request)
    {
        $stateId = $request->get('state_id');
        $cities = $this->shippingService->getCitiesByState($stateId);
        return response()->json($cities);
    }
    public function walletBalance()
    {
        $user = auth()->user();
        $wallet = $user->wallet;
        return response()->json([
            'balance' => $wallet ? $wallet->balance : 0
        ]);
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
        $outputPathEscaped = escapeshellarg($outputPath);
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

        $response =  response()->download($outputPath, 'waybills.pdf')->deleteFileAfterSend(true);
        
        foreach ($localFiles as $file) {
            $filePath = str_replace("'", "", $file);
        
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        return $response;

    }

    public function receiversByCompany($shippingCompanyId)
    {
        $receivers = $this->shippingService->receivers($shippingCompanyId);
        return response()->json($receivers);
    }

    public function sendersByCompany($shippingCompanyId)
    {
        $senders = $this->shippingService->senders($shippingCompanyId);
        return response()->json($senders);
    }

    public function getCitiesByCompanyAndCountry($shippingCompanyId)
    {
        $countryId = '65fd1a1c1fdbc094e3369b29';
        $cities = $this->shippingService->getCitiesByCompanyAndCountry($shippingCompanyId, $countryId);
        
        return response()->json($cities);
    }

}
