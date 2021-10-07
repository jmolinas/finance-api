<?php

namespace App\Models\Finance;

use App\Models\FinanceModel;
use App\Models\PaymentProcessors;
use Dev\Support\Models\Traits\Uuids;
use Illuminate\Database\Query\Builder;

class Wallet extends FinanceModel
{
    use Uuids;

    protected $table = 'wallets';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'currency_code',
        'type',
        'party_id',
        'status',
        'amount'
    ];

    /**
     * Wallet Logs related to Wallet
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany(WalletLogs::class, 'wallet_id', 'id');
    }

    /**
     * Collection wallet type filter
     *
     * @param Builder $query
     * @param int $partyId
     *
     * @return Builder
     */
    public function scopeTypeOf($query, $type)
    {
        return $query->whereType($type);
    }

    /**
     * Payment Processor Wallet
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function paymentProcessors()
    {
        return $this->hasOne(PaymentProcessors::class, 'party_id', 'party_id');
    }
}
