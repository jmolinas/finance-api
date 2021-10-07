<?php

namespace App\Models\Finance;

use App\Models\FinanceModel;
use Dev\Support\Models\Traits\Uuids;

class Ledger extends FinanceModel
{
    use \Staudenmeir\EloquentEagerLimit\HasEagerLimit;
    use Uuids;

    const UPDATED_AT = null;

    public $incrementing = false;

    protected $table = 'ledgers';

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
        'amount',
        'transaction_type'
    ];

    /**
     * Related Transfers record
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfers()
    {
        return $this->hasMany(Transfers::class, 'ledger_id', 'id');
    }
}
