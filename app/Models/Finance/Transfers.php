<?php

namespace App\Models\Finance;

use App\Models\FinanceModel;
use Dev\Support\Models\Traits\Uuids;
use Illuminate\Support\Facades\Schema;
use DB;

class Transfers extends FinanceModel
{
    use Uuids;
    use \Staudenmeir\EloquentEagerLimit\HasEagerLimit;

    protected $table = 'transfers';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_wallet_id',
        'to_wallet_id',
        'type',
        'details',
        'order_id',
        'transaction_id',
        'product_id',
        'sku',
        'ledger_id',
        'party_id',
        'metadata',
        'campaign_id',
        'amount',
        'transaction_date'
    ];

    /**
     * Mutate to Object
     *
     * @param string $value
     *
     * @return mixed
     */
    public function getMetadataAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * Related Ledger
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function ledger()
    {
        return $this->hasOne(Ledger::class, 'transfer_id');
    }

    /**
     * Gwt Transfers item
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        $columns = array_diff(Schema::getColumnListing($this->getTable()), ['amount']);
        return $this->hasMany(Transfers::class, 'order_id', 'order_id')
            ->withoutGlobalScope('api')
            ->select($columns)
            ->whereType('production_cost');
    }

    /**
     * Active transfers filter
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeTypeOf($query, $type)
    {
        $query->whereType($type)->whereNull('ledger_id');
    }

    /**
     * Cancelled Order filter
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $orderId
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeCancelledOrder($query, $orderId)
    {
        return $query->whereIn('type', ['sale', 'collection', 'base_cost', 'shipping'])
            ->orWhere('order_id', '=', $orderId)
            ->orWhereNull('ledger_id');
    }

    /**
     * Representation of Orders from Transfers sales
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeOrders($query)
    {
        $table = $this->getTable();
        return
            $query
            ->join(
                DB::raw("(SELECT distinct on (order_id) order_id, id FROM {$table} WHERE type = 'charges') AS q1"),
                function ($join) use ($table) {
                    $join
                        ->on("{$table}.order_id", '=', 'q1.order_id')
                        ->on("{$table}.id", '=', 'q1.id');
                }
            )
            ->whereType('charges');
    }
}
