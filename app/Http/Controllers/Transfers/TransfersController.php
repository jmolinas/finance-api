<?php

namespace App\Http\Controllers\Transfers;

use App\Http\Controllers\Controller;
use App\Models\Finance\Transfers;
use App\Transformers\AccountsSerializer;
use Dev\Support\Transformers\JsonApiSerializer;
use Illuminate\Http\Request;
use DB;

class TransfersController extends Controller
{
    /**
     * Transfers Collection
     *
     * @param Transfers $transfers
     * @param int $partyId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function collection(Request $request, Transfers $transfers)
    {
        $perPage = $request->input('page.size', 25);
        $charges = $transfers
            ->paginate($perPage)
            ->setPageName('page[number]');
        $results = $this->transform($charges, new JsonApiSerializer(), 'transfers');
        return $this->jsonPaginate($results);
    }

    /**
     * Accounts
     *
     * @param Transfers $transfers
     * @param AccountsSerializer $serializer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function accounts(Transfers $transfers, AccountsSerializer $serializer)
    {
        $transfer = $transfers
            ->select(
                'type',
                DB::raw("(CASE WHEN ledger_id IS NULL THEN 'unsettled' ELSE 'settled' END) AS transfer_status"),
                DB::raw("sum(amount) AS amount")
            )
            ->whereIn('type', ['collection', 'sale', 'shipping'])
            ->groupBy('transfer_status')
            ->groupBy('type')
            ->get()
            ->groupBy('type');
        $result = $serializer->transform($transfer)->getData();
        return $this->get(null, $result);
    }
}
