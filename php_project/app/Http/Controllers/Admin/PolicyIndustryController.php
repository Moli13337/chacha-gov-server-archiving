<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/13
 * Time: 17:59
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Repositories\Policy\PolicyIndustryRepository;
use App\Support\Collection;

class PolicyIndustryController extends Controller
{

    protected $repository;

    public function __construct(PolicyIndustryRepository $repository)
    {
        $this->repository = $repository;
    }

}