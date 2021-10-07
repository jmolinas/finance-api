<?php

namespace App\Models\Finance;

use App\Models\FinanceModel;

class WalletLogs extends FinanceModel
{
    const UPDATED_AT = null;

    protected $primaryKey = null;

    public $incrementing = false;
    
    protected $table = 'wallet_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'wallet_id',
        'transfer_id',
        'amount',
        'running_balance'
    ];

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'id', 'wallet_id');
    }
}
