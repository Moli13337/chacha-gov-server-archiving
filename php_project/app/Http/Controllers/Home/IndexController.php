<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/22
 * Time: 11:35
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Repositories\Policy\PolicyRepository;
use App\Repositories\Policy\ProjectRepository;
use App\Support\Collection;
use Illuminate\Http\Request;
use tests\Mockery\Adapter\Phpunit\EmptyTestCase;

class IndexController extends Controller
{

    protected $repository;
    protected $projectRepository;

    public function __construct(PolicyRepository $repository,ProjectRepository $projectRepository)
    {

        $this->repository = $repository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * FUNCTION_NAME : policy
     * author : jp
     * 最新政策
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function policy(Request $request)
    {

        $params = $request->only(['per_page']);
        $params['order_by'] = [
            'id' => 'DESC'
        ];

        $column = [
            'enc_id',
            'name',
            'obj_type',
            'publish_status',
            'validity_edate',
            'created_at'
        ];

        $params['obj_type'] = [
            OBJ_TYPE['macro_policy'],
            OBJ_TYPE['sup_policy'],
            OBJ_TYPE['imple_regu'],
        ];
        $params['publish_status'] = PUBLISH_STATUS['yes'];
        $res = $this->repository->getIndexNewPolicy($params, $column);

        if (empty($res['data'])) {
            return codeRender(Code::OK, $res);
        }
        foreach ($res['data'] as $key => $value) {
            $res['data'][$key]['id'] = $value['enc_id'];
            $res['data'][$key]['is_new'] = ($value['created_at'] < (time() -  7*24*60*60)) ? 0 : 1;
        }
        return codeRender(Code::OK, $res);
    }

    /**
     * FUNCTION_NAME : publicity
     * author : jp
     * 最新公示
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function publicity(Request $request)
    {
        $params = $request->only(['per_page']);
        $params['order_by'] = [
            'id' => 'DESC'
        ];

        $column = [
            'enc_id',
            'obj_type',
            'name',
            'publish_status',
            'validity_edate',
            'created_at'
        ];

        $params['obj_type'] = [
            OBJ_TYPE['announce'],
            OBJ_TYPE['publicity'],
            OBJ_TYPE['approval'],
        ];
        $params['publish_status'] = PUBLISH_STATUS['yes'];

        $res = $this->repository->getIndexNewPolicy($params, $column);
        if (empty($res['data'])) {
            return codeRender(Code::OK, $res);
        }
        foreach ($res['data'] as $key => $value) {
            $res['data'][$key]['id'] = $value['enc_id'];
        }
        return codeRender(Code::OK, $res);
    }

    /**
     * FUNCTION_NAME : project
     * author : jp
     * 最新申报
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     */
    public function project(Request $request)
    {
        $params = $request->only(['per_page']);
        $params['order_by'] = [
            'id' => 'DESC'
        ];
        $column = [
            'enc_id',
            'name',
            'created_at'
        ];
        $params['publish_status'] = PUBLISH_STATUS['yes'];

        $res = $this->projectRepository->getIndexNew($params, $column);
        if (empty($res['data'])) {
            return codeRender(Code::OK, $res);
        }
        foreach ($res['data'] as $key => $value) {
            $res['data'][$key]['id'] = $value['enc_id'];
        }
        return codeRender(Code::OK, $res);
    }
}