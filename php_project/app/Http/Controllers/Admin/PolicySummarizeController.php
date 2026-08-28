<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/13
 * Time: 17:59
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Repositories\Policy\PolicySummarizeRepository;
use App\Support\Collection;

class PolicySummarizeController extends Controller
{

    protected $repository;

    public function __construct(PolicySummarizeRepository $repository)
    {
        $this->repository = $repository;
    }

}