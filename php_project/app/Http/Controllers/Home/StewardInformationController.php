<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/7
 * Time: 9:59
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\StewardInformation\HomeDetailRequest;
use App\Http\Requests\StewardInformation\HomeListRequest;
use App\Repositories\Steward\StewardInformationRepository;

class StewardInformationController extends Controller
{

    protected $repository;
    public function __construct(StewardInformationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list(HomeListRequest $request)
    {
        $params = $this->filter($request);
        $params['publish_status'] = PUBLISH_STATUS['yes'];

        $params['order_by'] = ['id' => 'DESC'];
        $column = [
            'id',
            'enc_id',
            'title',
            'content',
            'source_name',
            'type',
            'link',
            'publish_status',
            'publish_time',
            'created_at',
        ];
        $data = $this->repository->clientList($params, $column);
        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        foreach ($data['data'] as $key => &$value) {
            $value['id'] = $value['enc_id'];
        }

        return codeRender(Code::OK, $data);
    }

    public function detail(HomeDetailRequest $request)
    {
        $where = [
            'enc_id' => $request->input('id'),
            'publish_status' => PUBLISH_STATUS['yes']
        ];
        $column = [
            'id',
            'enc_id',
            'title',
            'content',
            'source_name',
            'type',
            'link',
            'publish_status',
            'publish_time',
            'created_at',
        ];
        $data = $this->repository->detail($where, $column);
        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }
        unset($data['publish_staff']);
        $data['id'] = $data['enc_id'];
        return codeRender(Code::OK, $data);
    }
}