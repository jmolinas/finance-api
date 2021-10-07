<?php

namespace App\Services\Transfer;

use App\Models\Finance\Transfers;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Party;
use Illuminate\Database\Eloquent\Collection;

class TransfersFetcher
{
    protected $transfers;

    protected $party;

    protected $collection;

    protected $orderIds = [];

    protected $orderItemIds = [];

    protected $params = [
            'from' => null,
            'to' => null,
            'transfer_ids' => []
        ],
        $type;

    const TYPE = [
        'disbursement',
        'sale',
        'collection',
        'settlement',
        'return',
        'production_cost',
        'shipping',
        'chargeback',
        'revenue'
    ];

    /**
     * Set Collection
     *
     * @param Collection $collection
     * 
     * @return static
     */
    public function setCollection(Collection $collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Transfer Fetcher
     *
     * @param Transfers $transfers
     */
    public function __construct(Transfers $transfers)
    {
        $this->transfers = $transfers;
    }

    /**
     * Set Party
     *
     * @param Party $party
     * 
     * @return static
     */
    public function setParty(Party $party)
    {
        $this->party = $party;
        $this->transfers->where('party_id', $this->party->id);
        return $this;
    }

    /**
     * Set Type
     *
     * @param string $type
     * 
     * @return static
     */
    public function setType($type)
    {
        if (in_array($type, static::TYPE) === false) {
            throw new \RuntimeException('Invalid Transfer type');
        }
        $this->type = $type;
        return $this;
    }

    /**
     * Set Parameters
     *
     * @param array $transferIds
     * @param string|date $from
     * @param string|date $to
     * 
     * @return void
     */
    public function setParams(array $transferIds = [], $from = null, $to = null)
    {
        $this->params = [
            'transfer_ids' => $transferIds,
            'from' => $from,
            'to' => $to,
        ];
        $this->transfers
            ->when(
                empty($this->params['transfer_ids']) !== true,
                function ($query) {
                    $query->whereIn('id', $this->params['transfer_ids']);
                }
            )
            ->when(
                $this->params['from'] !== null && $this->params['to'] !== null,
                function ($query) {
                    $query->where(
                        [
                            ['created_at', '>=', $this->params['from']],
                            ['created_at', '<', $this->params['to']]
                        ]
                    );
                }
            );

        return $this;
    }

    /**
     * Filter Order id
     *
     * @param array $orderIds
     * 
     * @return TransfersFetcher
     */
    public function setTransactionIds(array $orderIds)
    {
        $this->orderIds = $orderIds;
        $this->transfers->whereIn('transaction_id', $this->orderIds);
        return $this;
    }

    /**
     * Filter Order Item id
     *
     * @param array $orderItemIds
     * 
     * @return TransfersFetcher
     */
    public function setOrderItemIds(array $orderItemIds)
    {
        $this->orderItemIds = $orderItemIds;
        $this->transfers->whereIn('transaction_id', $this->orderItemIds);
        return $this;
    }

    /**
     * Return Transactions Eloquent Builder
     *
     * @return Builder
     */
    public function builder(): Builder
    {
        return $this->transfers
            ->typeOf($this->type)
            ->whereNotNull('party_id')
            ->lockForUpdate();
    }

    /**
     * Get Ledger Collection
     *
     * @return Collection
     */
    public function get()
    {
        return $this->collection !== null ?
            $this->collection :
            $this->builder()->get();
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function type()
    {
        return $this->type;
    }
}
