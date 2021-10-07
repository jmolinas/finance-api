<?php

namespace App\Models;

class PaymentProcessors extends CoreModel
{
    protected $table = 'payment_processors';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'machine_name',
        'party_id'
    ];
}
