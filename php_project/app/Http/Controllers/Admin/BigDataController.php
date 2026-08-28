<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/18
 * Time: 19:06
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\BigData\DeleteRequest;
use App\Http\Requests\BigData\DetailRequest;
use App\Http\Requests\BigData\ListRequest;
use App\Http\Requests\BigData\PartitionRequest;
use App\Http\Requests\BigData\UnHandleRequest;
use App\Repositories\Policy\BigDataRepository;

class BigDataController extends Controller
{

    protected $repository;

    public function __construct(BigDataRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * FUNCTION_NAME : unHandle
     * author : jp
     * 待处理列表
     * @param UnHandleRequest $request
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     */
    public function unHandle(UnHandleRequest $request)
    {
        $params = $this->filter($request);
        // 处理排序
//        if (!empty($params['sort_pub']) && $params['sort_pub'] == SORT_PUB['desc']) {
//            $params['order_by'] = [
//                'pub_time' => 'DESC'
//            ];
//        } elseif (!empty($params['sort_pub']) && $params['sort_pub'] == SORT_PUB['asc']) {
//            $params['order_by'] = [
//                'pub_time' => 'ASC'
//            ];
//        }
        if (!empty($params['sort']) && $params['sort'] == SORT['asc']) {
            $params['order_by']['id'] = 'ASC';
        } else {
            $params['order_by']['id'] = 'DESC';
        }
        $params['is_handle'] = BIG_DATA_HANDLE['no'];
        $data = $this->repository->search($params);
        return codeRender(Code::OK, $data);
    }

    public function deleteBatch(DeleteRequest $request)
    {
        $this->repository->deleteBatch($request->input('ids'));
        return codeRender(Code::OK);
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);

//        // 处理排序
//        if (!empty($params['sort_pub']) && $params['sort_pub'] == SORT_PUB['desc']) {
//            $params['order_by'] = [
//                'pub_time' => 'DESC'
//            ];
//        } elseif (!empty($params['sort_pub']) && $params['sort_pub'] == SORT_PUB['asc']) {
//            $params['order_by'] = [
//                'pub_time' => 'ASC'
//            ];
//        }

        if (!empty($params['sort']) && $params['sort'] == SORT['asc']) {
            $params['order_by']['id'] = 'ASC';
        } else {
            $params['order_by']['id'] = 'DESC';
        }

        $params['obj_type'] = 0;
        $params['is_handle'] = BIG_DATA_HANDLE['no'];
        $data = $this->repository->search($params);

        return codeRender(Code::OK, $data);
    }

    public function partition(PartitionRequest $request)
    {
        $this->repository->updatePartition($request->input('ids'), $request->input('obj_type'));

        return codeRender(Code::OK);
    }

    public function detail(DetailRequest $request)
    {
        $data = $this->repository->findRepository($request->input('id'));

        return codeRender(Code::OK, $data);
    }
}