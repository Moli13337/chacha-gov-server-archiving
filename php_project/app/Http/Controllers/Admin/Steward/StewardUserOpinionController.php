<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 14:38
 */

namespace App\Http\Controllers\Admin\Steward;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\StewardUserOpinion\DetailRequest;
use App\Http\Requests\StewardUserOpinion\ListRequest;
use App\Repositories\Steward\StewardUserOpinionRepository;

class StewardUserOpinionController extends Controller
{

    protected $repository;

    public function __construct(StewardUserOpinionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];
        $data = $this->repository->list($params);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }
        return codeRender(Code::OK, $data);
    }

    public function detail(DetailRequest $request)
    {
        $where = [
            'id' => $request->input('id')
        ];
        $data = $this->repository->detail($where);
        return codeRender(Code::OK, $data);
    }
}