<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Dev\Support\Http\ModelTransformer;
use Dev\Support\Http\ApiResponse;
use Dev\Support\Models\Traits\Filterable;
use Dev\Support\Models\Traits\Row;
use Dev\Support\Models\Traits\Searchable;
use Dev\Support\Models\Traits\Sortable;

class Controller extends BaseController
{
    use ModelTransformer, ApiResponse, Sortable, Filterable, Row, Searchable;
}
