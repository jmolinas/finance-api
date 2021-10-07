<?php

namespace App\Models;

class Partners extends CoreModel
{
    protected $table = 'partners';

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
