<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/12/9
 * Time: 15:06
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentEnterprise\DeleteRequest;
use App\Http\Requests\AgentEnterprise\ListRequest;
use App\Http\Requests\AgentEnterprise\UpdatePublishRequest;
use App\Repositories\Agent\AgentEnterpriseRepository;
use App\Repositories\Agent\AgentRepository;

class AgentEnterpriseController extends Controller
{

    protected $repository;
    public function __construct(AgentEnterpriseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     *
     * @api GET /api/agent/enterprise/list 中介机构列表
     * @apiVersion 1.0.0
     * @apiName 中介机构列表
     * @apiGroup 运营端--中介机构
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} keyword
     * @apiParam {Number} type_id
     * @apiParam {Number} page
     * @apiParam {Number} per_page
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
     *         "data":{
                    "total": 4,
                    "total_page": 1,
                    "current_page": 1,
                    "per_page_num": 4,
                    "data": [
                        {
                        "id": 115,
                        "name": "成都安箭建材有限公司",
                        "type_name": "科技创新",
                        "user_name": "",
                        "user_mobile": ""
                        "publish_status": 1
                        },
                    ]
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function list(ListRequest $request)
    {
        $param = $this->filter($request);
        $param['order_by'] = ['id' => 'DESC'];
        $data = $this->repository->list($param, ['id', 'name']);
        return codeRender(Code::OK, $data);

    }

    /**
     *
     * @api POST /api/agent/enterprise/publish 上下架
     * @apiVersion 1.0.0
     * @apiName 上下架
     * @apiGroup 运营端--中介机构
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} id
     * @apiParam {Number} publish_status 上下级 1-上架 0-下架
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
    public function updatePublish(UpdatePublishRequest $request)
    {
        $res = app(AgentRepository::class)->updatePublishByEnterprise($request->input('id'), $request->input('publish_status'));

        return codeRender(Code::OK, $res);
    }

    /**
     *
     * @api POST /api/agent/enterprise/delete 删除
     * @apiVersion 1.0.0
     * @apiName 中介机构删除
     * @apiGroup 运营端--中介机构
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

     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function delete(DeleteRequest $request)
    {
        $res = app(AgentRepository::class)->deleteByEnterprise($request->input('id'));

        return codeRender(Code::OK);
    }
}
