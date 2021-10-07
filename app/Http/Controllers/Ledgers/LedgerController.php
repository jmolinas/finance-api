<?php

namespace App\Http\Controllers\Ledgers;

use App\Http\Controllers\Controller;
use App\Models\Finance\Ledger;
use Dev\Support\Transformers\JsonApiSerializer;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    /**
     * List Ledger Sale
     *
     * @param Request $request
     * @param Ledger $ledger
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function collection(Request $request, Ledger $ledger)
    {
        $perPage = $request->input('page.size', 25);
        $collectionLedger = $ledger
            ->with(
                [
                    'transfers' => function ($query) {
                        $query->latest()->limit(25);
                    }
                ]
            )
            ->paginate($perPage)
            ->setPageName('page[number]');
        $results = $this->transform($collectionLedger, new JsonApiSerializer(), 'ledger');
        return $this->jsonPaginate($results);
    }
}
