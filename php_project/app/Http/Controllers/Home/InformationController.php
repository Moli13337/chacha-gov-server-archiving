<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 17:25
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\Information\DeleteInformationRequest;
use App\Http\Requests\Information\DetailInformationRequest;
use App\Http\Requests\Information\SaveInformationRequest;
use App\Http\Requests\Information\UpdateInformationRequest;
use App\Repositories\InformationRepository;
use App\Support\Collection;
use Illuminate\Http\Request;

class InformationController extends Controller
{

    protected $repository;
    public function __construct(InformationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function detail(DetailInformationRequest $request)
    {
        $data = $this->repository->findRepository($request->input('id'));

        return codeRender(Code::OK, $data);
    }

    public function list(Request $request)
    {
        $params = $request->all();

        $params['order_by'] = [
            'created_at' => 'DESC'
        ];
        $data = $this->repository->search($params);
        return codeRender(Code::OK, $data);
    }


}