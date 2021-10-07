<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends CoreModel
{
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];
}
