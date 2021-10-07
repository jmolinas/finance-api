<?php

namespace App\Services\Transfer\Factories;

use App\Models\Finance\Transfers;
use App\Models\Party;

interface TransfersInterface
{
    /**
     * Set metadata
     *
     * @param array $metadata
     * 
     * @return static
     */
    public function setMetadata(array $metadata);
    
    /**
     * Process sales transfer
     *
     * @param string $orderId
     * @param string $itemId
     * @param string $campaignId
     * @param numeric $amount
     * 
     * @return Transfers
     */
    public function process($orderId, $itemId, $campaignId, $amount, $productId, $sku = null, $transactionDate = null) : Transfers;

     /**
     * Set Party
     *
     * @param Party $party
     * 
     * @return static
     */
    public function setParty(Party $party);
}