<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseFormRequest;
use App\Support\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function filter(BaseFormRequest $request)
    {
        return Collection::filter(array_keys($request->rules()), $request->all());
    }
}
