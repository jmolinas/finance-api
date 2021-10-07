<?php

namespace App\Http\Controllers\Transfers;

use App\Http\Controllers\Controller;
use App\Models\Finance\Transfers;
use Dev\Support\Transformers\JsonApiSerializer;
use Illuminate\Http\Request;

class ChargeTransfersController extends Controller
{
    /**
     * Charges list by party_id
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
            ->orders()
            ->paginate($perPage)
            ->setPageName('page[number]');
        $results = $this->transform($charges, new JsonApiSerializer(), 'transfers');
        return $this->jsonPaginate($results);
    }
}