<?php

namespace App\Http\Controllers;

use App\Models\Finance\Transfers;
use App\Models\Finance\WalletLogs;
use App\Services\ChargebackOrders;
use App\Services\ReturnOrders;
use App\Services\Transfer\Factories\ChargebackTransfers;
use Illuminate\Http\Request;
use Dev\Support\Transformers\JsonApiSerializer;
use DB;

class OrdersController extends Controller
{
    protected $filterable = ['created_at'];

    protected $orders;

    /**
     * Orders Controller
     *
     * @param Transfers $transfers
     */
    public function __construct(Transfers $transfers)
    {
        $this->orders = $transfers->with('items')->orders();
    }

    /**
     * Orders Collection
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function collection(Request $request)
    {
        $perPage = $request->input('page.size', 25);
        $sort = $request->input('sort', null);
        $orders = $this->orders
            ->when(
                $sort === null,
                function ($query) {
                    $query->orderBy('created_at', 'DESC');
                }
            )
            ->paginate($perPage)
            ->setPageName('page[number]');
        $results = $this->transform($orders, new JsonApiSerializer(), 'orders');
        return $this->jsonPaginate($results);
    }

    /**
     * Order Logs by Month
     *
     * @param Request $request
     * @param WalletLogs $logs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(WalletLogs $logs)
    {
        return $this->get(
            null,
            $logs
                ->selectRaw("DATE_TRUNC('month', created_at)::date as month")
                ->selectRaw("ABS(SUM(amount)) as total")
                ->whereHas(
                    'wallet',
                    function ($query) {
                        $query->whereType('order');
                    }
                )
                ->groupBy(DB::raw("DATE_TRUNC('month', created_at)"))
                ->get()
        );
    }

    /**
     * Order Logs detailed
     *
     * @param Request $request
     * @param WalletLogs $logs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logs(Request $request, WalletLogs $logs)
    {
        return $this->get(
            null,
            $logs->select(DB::raw('ABS(amount) as amount'), 'created_at')
                ->whereHas(
                    'wallet',
                    function ($query) {
                        $query->whereType('order');
                    }
                )
                ->get()
        );
    }

    /**
     * ChargeBack
     *
     * @param Request $request
     * @param ChargebackTransfers $chargeback
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function chargeBack(Request $request, ChargebackOrders $chargeback)
    {
        $rules = [
            'order_id' => 'required|string',
            'charge' => 'required|boolean'
        ];
        if ($request->charge) {
            $rules['charge_amount'] = 'required|numeric';
        }
        $this->validate($request, $rules);
        if ($request->charge === 1) {
            $chargeback->charge($request->charge_amount);
        }

        return $this->accepted($chargeback->process($request->order_id));
    }

    /**
     * Refund Order
     *
     * @param Request $request
     * @param ReturnOrders $refund
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refund(Request $request, ReturnOrders $refund)
    {
        $rules = [
            'order_id' => 'required|string',
            'charge' => 'required|min:0:max:1'
        ];
        if ($request->charge === 1) {
            $rules['charge_amount'] = 'required|numeric|gt:0';
        }
        $this->validate($request, $rules);
        if ($request->charge === 1) {
            $refund->charge($request->charge_amount);
        }
        return $this->accepted($refund->process($request->order_id));
    }
}
