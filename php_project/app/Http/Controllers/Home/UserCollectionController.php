<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/12
 * Time: 10:12
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\DistrictService;
use App\Http\Controllers\Service\PolicyService;
use App\Http\Requests\UserCollection\ListRequest;
use App\Http\Requests\UserCollection\SaveRequest;
use App\Models\AgentModel;
use App\Models\PolicyModel;
use App\Models\ProjectModel;
use App\Repositories\Agent\AgentRepository;
use App\Repositories\Policy\ProjectRepository;
use App\Repositories\User\UserCollectionRepository;

class UserCollectionController extends Controller
{

    protected $repository;
    public function __construct(UserCollectionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     *
     * @api post /home/collection/save 收藏
     * @apiVersion 1.0.0
     * @apiName CollectionSave
     * @apiGroup 客户端--收藏--保存
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} obj_enc_id 必填 客户端使用的id
     * @apiParam {Number} obj_type 必填 类型 1-宏观政策 4-申报公示公告 7-活动公示公告 8-项目 10-拨款公示公告 16-中介机构
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
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function save(SaveRequest $request)
    {
        $params = $this->filter($request);

        $enc = $request->input('obj_enc_id');
        $obj_type = $request->input('obj_type');
        $res = [];
        $where = [
            'enc_id' => $enc,
            'publish_status' => PUBLISH_STATUS['yes'],
        ];
        $policy = [
            OBJ_TYPE['macro_policy'],
            OBJ_TYPE['announce'],
            OBJ_TYPE['publicity'],
            OBJ_TYPE['approval'],
        ];
        if (in_array($obj_type, $policy)) {
            $res = PolicyModel::select(['id'])->where($where)->first();
        } elseif ($obj_type == OBJ_TYPE['project']) {
            $res = ProjectModel::select(['id'])->where($where)->first();
        } elseif ($obj_type == OBJ_TYPE['agent']) {
            $res = AgentModel::select(['id'])->where($where)->first();
        }
        if (empty($res)) {
            return codeRender(Code::PARAM_ERROR);
        }
        $obj_id = $res['id'];
        $user_id = (int)getLoginHome('id');
        $params['user_id'] = $user_id;
        $params['obj_id'] = $obj_id;

        $has = $this->repository->hasCollection(array_except($params, ['obj_enc_id']));
        if ($has) {
            return codeRender(Code::USER_COLLECTION_EXIST_ERROR);
        }
        $this->repository->storeRepository($params);
        return codeRender(Code::OK);
    }

    /**
     *
     * @api post /home/collection/cancel 取消收藏
     * @apiVersion 1.0.0
     * @apiName CollectionCancel
     * @apiGroup 客户端--收藏--取消
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeader {String} Parm 其他参数
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} obj_enc_id 必填 客户端使用的id
     * @apiParam {Number} obj_type 必填 类型 1-宏观政策 4-申报公示公告 7-活动公示公告 8-项目 10-拨款公示公告 16-中介机构
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
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function cancel(SaveRequest $request)
    {
        $params = $this->filter($request);
        $user_id = (int)getLoginHome('id');
        $params['user_id'] = $user_id;

        $enc = $request->input('obj_enc_id');

        $obj_type = $request->input('obj_type');
        $res = [];
        $where = [
            'enc_id' => $enc,
            'publish_status' => PUBLISH_STATUS['yes'],
        ];
        $policy = [
            OBJ_TYPE['macro_policy'],
            OBJ_TYPE['announce'],
            OBJ_TYPE['publicity'],
            OBJ_TYPE['approval'],
        ];
        if (in_array($obj_type, $policy)) {
            $res = PolicyModel::select(['id'])->where($where)->first();
        } elseif ($obj_type == OBJ_TYPE['project']) {
            $res = ProjectModel::select(['id'])->where($where)->first();
        } elseif ($obj_type == OBJ_TYPE['agent']) {
            $res = AgentModel::select(['id'])->where($where)->first();
        }

        if (empty($res)) {
            return codeRender(Code::PARAM_ERROR);
        }

        $has = $this->repository->hasCollection(array_except($params, ['obj_enc_id']));
        if (!$has) {
            return codeRender(Code::USER_COLLECTION_NO_EXIST_ERROR);

        }

        $this->repository->cancelCollection($params);
        return codeRender(Code::OK);
    }

    /**
     *
     * @api get /home/collection/list 收藏列表
     * @apiVersion 1.0.0
     * @apiName CollectionList
     * @apiGroup 客户端--收藏--列表
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *     }
     *
     * @apiParam {Number} obj_type 收藏的类型  类型 1-宏观政策 4-申报公示公告 7-活动公示公告 8-项目 10-拨款公示公告 16-中介机构
     * @apiParam {Number} per_page
     * @apiParam {Number} page
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
    {
    "code": 200,
    "message": "操作成功",
    "data": {
    "total": 1,
    "total_page": 1,
    "current_page": 1,
    "per_page_num": 1,
    "data": [
    {
    "id": "04b607a8ae9237fa6cd9",
    "enc_id": "04b607a8ae9237fa6cd9",
    "code": "20191024000001",
    "obj_type": 1,
    "name": "市科技局关于申报高端人才外籍子女就读国际学校专项资助的通知",
    "doc_num": "嗯嗯",
    "content": "<p>嗯嗯嗯<br/></p>",
    "content_name": "注册证申请表.pdf",
    "content_url": "https://xkd-saas-base.oss-cn-beijing.aliyuncs.com/dev-wenjiang/20191024/0/ypQF3X0mLYcBb3pSimWzHE8gaz8YXY0mPN5Lfs98.pdf",
    "pub_time": 1558886400,
    "province_code": 110000000000,
    "city_code": 110100000000,
    "district_code": 0,
    "validity_sdate": 1572278400,
    "validity_edate": 1574179199,
    "publish_status": 1,
    "source": "",
    "source_web": "天津市科学技术委员会",
    "source_url": "额鹅鹅鹅",
    "is_handel": -1,
    "big_data_id": 203765,
    "target_id": 0,
    "original_policy_id": 0,
    "created_staff_id": 1,
    "created_at": "1571887873",
    "updated_at": "1571887873",
    "deleted_at": null,
    "publish_status_desc": "已发布",
    "expired": 0,
    "expired_desc": "未过期",
    "obj_type_name": "",
    "province_name": "北京",
    "city_name": "北京",
    "district_name": ""
    }
    ]
    }
    }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];
        $params['user_id'] = (int)getLoginHome('id');

        $data = $this->repository->clientList($params);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        $policyIds = [];
        $agentIds = [];
        $projectIds = [];
        foreach ($data['data'] as $key => $val) {
            if ($val['obj_type'] == OBJ_TYPE['agent']) {
                $agentIds[] = $val['obj_id'];
            } elseif ($val['obj_type'] == OBJ_TYPE['project']) {
                $projectIds[] = $val['obj_id'];
            } else {
                $policyIds[] = $val['obj_id'];
            }
        }

        $policy = app(PolicyService::class)->getCollectionByIds($policyIds);
//        $policy = app(DistrictService::class)->getDistrictNameList($policy);
        $policy = array_column($policy, null, 'id');
        $project = app(ProjectRepository::class)->collectionByIds($projectIds);
//        $project = app(DistrictService::class)->getDistrictNameList($project);

        $project = array_column($project, null, 'id');
        $agent = app(AgentRepository::class)->collectionByiIds($agentIds);
        $agent = array_column($agent, null, 'id');

        foreach ($data['data'] as $key => $value) {
            $id = $value['obj_id'];
            if ($value['obj_type'] == OBJ_TYPE['agent']) {
                if (empty($agent[$id])) {
                    unset($data['data'][$key]);
                } else {
                    $value = $agent[$id];
                    $value['id'] = $value['enc_id'];
                    $value['obj_type'] = OBJ_TYPE['agent'];
                    $data['data'][$key] = $value;

                }
            }  elseif ($value['obj_type'] == OBJ_TYPE['project']) {
                if (empty($project[$id])) {
                    unset($data['data'][$key]);
                } else {
                    $value = $project[$id];
                    $value['id'] = $value['enc_id'];
                    $value['obj_type'] = OBJ_TYPE['project'];
                    $data['data'][$key] = $value;
                }
            }  else {
                if (empty($policy[$id])) {
                    unset($data['data'][$key]);
                } else {
                    $value = $policy[$id];
                    $value['id'] = $value['enc_id'];
                    $data['data'][$key] = $value;
                }
            }
        }

        return codeRender(Code::OK, $data);
    }
}