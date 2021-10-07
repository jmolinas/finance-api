<?php

namespace App\Models;

use App\Models\Finance\Wallet;

class Party extends CoreModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'parties';

    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type'];

    /**
     * Affiliate Record Associated with Party.
     */
    public function affiliate()
    {
        return $this->hasOne(Affiliates::class, 'party_id', 'id');
    }

    /**
     * User Record Associated with Party.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'party_id', 'id');
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class, 'party_id', 'id');
    }

    public function partners()
    {
        return $this->hasMany(Partners::class, 'party_id', 'id');
    }

    public function paymentProcessors()
    {
        return $this->hasMany(PaymentProcessors::class, 'party_id', 'id');
    }
}
