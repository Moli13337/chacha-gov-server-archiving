<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:24
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentType\DeleteRequest;
use App\Http\Requests\AgentType\StoreChildrenRequest;
use App\Http\Requests\AgentType\StoreRequest;
use App\Http\Requests\AgentType\UpdateRequest;
use App\Repositories\Agent\AgentRepository;
use App\Repositories\Agent\AgentTypeRepository;
use Illuminate\Http\Request;

class AgentTypeController extends Controller
{

    protected $agentTypeRepository;

    public function __construct(AgentTypeRepository $agentTypeRepository)
    {
        $this->agentTypeRepository = $agentTypeRepository;
    }

    public function all(Request $request)
    {
        return codeRender(Code::OK, $this->agentTypeRepository->getAll(['id', 'name']));
    }

    public function firstClass(Request $request)
    {
        return codeRender(Code::OK, $this->agentTypeRepository->firstClass(['id', 'name']));
    }

    /**
     *
     * @api GET /api/agent/type/list 列表
     * @apiVersion 1.0.0
     * @apiName 列表
     * @apiGroup 运营端--中介服务类型配置
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} Parm1 参数1
     * @apiParam {Number} Parm2 参数2
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "message": "操作成功",
     *       "data":[
                    {
                        "id": 1,
                        "name": "创业服务",
                        "parent_id": 0
                    },
                    {
                    "id": 12,
                    "name": "test服务",
                    "parent_id": 0,
                    "children": [
                            {
                                "id": 13,
                                "name": "test服务",
                                "parent_id": 12
                            },
                            {
                                "id": 14,
                                "name": "test服务3",
                                "parent_id": 12
                            }
                    ]
                    }
             ]
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function list(Request $request)
    {
        $data = $this->agentTypeRepository->list(['id', 'name', 'parent_id']);
        return codeRender(Code::OK, $data);
    }

    /**
     *
     * @api POST /api/agent/type/store 新增一级
     * @apiVersion 1.0.0
     * @apiName 新增一级
     * @apiGroup 运营端--中介服务类型配置
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} name
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "message": "操作成功",
     *       "data":{
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function store(StoreRequest $request)
    {
        $data = $this->filter($request);
        $res = $this->agentTypeRepository->storeRepository($data);
        return codeRender(Code::OK, $res);
    }

    /**
     *
     * @api POST /api/agent/type/storeChildren 新增二级
     * @apiVersion 1.0.0
     * @apiName 新增二级
     * @apiGroup 运营端--中介服务类型配置
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} name
     * @apiParam {Number} parent_id
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "message": "操作成功",
     *       "data":{
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function storeChildren(StoreChildrenRequest $request)
    {
        $data = $this->filter($request);
        $res = $this->agentTypeRepository->storeRepository($data);
        return codeRender(Code::OK, $res);
    }

    /**
     *
     * @api POST /api/agent/type/delete 删除
     * @apiVersion 1.0.0
     * @apiName 删除
     * @apiGroup 运营端--中介服务类型配置
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} id
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "message": "操作成功",
     *       "data":{
     *              "field-1": "xx",
     *              "field-2": "xx",
     *              "field-3": "xx"
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function delete(DeleteRequest $request)
    {

        $id = $request->input('id');
        $data = $this->agentTypeRepository->findRepository($id);
        if ($data['reserved'] == RESERVED_YES) {
            return codeRender(Code::AGENT_TYPE_DELETE_RESERVED);
        } elseif (empty($data['parent_id'])) {
            $has = app(AgentRepository::class)->hasByType($id);
            if ($has) {
                return codeRender(Code::AGENT_TYPE_DELETE_AGENT);
            }
        }



        $res = $this->agentTypeRepository->deleteRepository($request->input('id'));
        return codeRender(Code::OK, $res);
    }

    /**
     *
     * @api POST /api/agent/type/update 更新
     * @apiVersion 1.0.0
     * @apiName 更新
     * @apiGroup 运营端--中介服务类型配置
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} Parm1 参数1
     * @apiParam {Number} Parm2 参数2
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "message": "操作成功",
     *       "data":{
     *              "field-1": "xx",
     *              "field-2": "xx",
     *              "field-3": "xx"
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function update(UpdateRequest $request)
    {
        $data = $this->filter($request);
        $data = array_except($data, ['parent_id']);

        $org = $this->agentTypeRepository->findRepository($data['id']);
        if ($org['reserved'] == RESERVED_YES) {
            return codeRender(Code::AGENT_TYPE_UPDATE_RESERVED);
        }
        $res = $this->agentTypeRepository->updateRepository($data);
        return codeRender(Code::OK, $res);
    }

}