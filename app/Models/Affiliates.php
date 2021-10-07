<?php

namespace App\Models;

class Affiliates extends CoreModel
{
    protected $table = 'affiliates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'external_id',
        'party_id',
        'name',
        'email'
    ];
}
