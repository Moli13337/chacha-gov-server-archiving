<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/5
 * Time: 15:25
 */

namespace App\Http\Controllers\Admin\Share;


use App\Common\Code;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\DistrictService;
use App\Http\Requests\ShareActivity\ApplyListRequest;
use App\Http\Requests\ShareActivity\DeleteRequest;
use App\Http\Requests\ShareActivity\DetailRequest;
use App\Http\Requests\ShareActivity\ListRequest;
use App\Http\Requests\ShareActivity\SaveRequest;
use App\Http\Requests\ShareActivity\UpdateRequest;
use App\Repositories\Share\ShareActivityApplyRepository;
use App\Repositories\Share\ShareActivityRepository;
use Illuminate\Support\Facades\DB;

class ShareActivityController extends Controller
{

    public $repository;
    public function __construct(ShareActivityRepository $repository)
    {
        $this->repository = $repository;
    }

    public function save(SaveRequest $request)
    {
        $params = $this->filter($request);
        $params[CREATED_STAFF_ID] = (int)getLoginStaff('id');
        $params['enc_id'] = $this->getEncId();
        if ($params['publish_status'] == PUBLISH_STATUS['yes']) {
            $params['publish_time'] = time();
            $params[PUBLISH_STAFF_ID] = (int)getLoginStaff('id');
        }

        $params['status'] = $this->repository->computeStatus($params['validity_sdate'], $params['validity_edate']);

        try {
            $res = $this->repository->storeRepository($params);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Exception $e) {
            throw new QueryException(Code::FAIL, $e->getMessage());
        }
        return codeRender(Code::OK);

    }

    public function update(UpdateRequest $request)
    {
        $params = $this->filter($request);
        $origin = $this->repository->simpleDetail(['id' => $params['id']], ['publish_status']);

        if ($params['publish_status'] == PUBLISH_STATUS['yes'] && $origin['publish_status'] !=  PUBLISH_STATUS['yes']) {
            $params['publish_time'] = time();
            $params[PUBLISH_STAFF_ID] = (int)getLoginStaff('id');
        } elseif ($params['publish_status'] == PUBLISH_STATUS['no'] && $origin['publish_status'] !=  PUBLISH_STATUS['no']) {
            $params['publish_time'] = 0;
            $params[PUBLISH_STAFF_ID] = 0;
        }
        $params['status'] = $this->repository->computeStatus($params['validity_sdate'], $params['validity_edate']);

        try {
            $res = $this->repository->updateRepository($params);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Exception $e) {
            throw new QueryException(Code::FAIL, $e->getMessage());
        }
        return codeRender(Code::OK);
    }

    public function delete(DeleteRequest $request)
    {
        // 这里需要做是否有人报名的判断
        $where = [
            'activity_id' => $request->input('id')
        ];
        $count = app(ShareActivityApplyRepository::class)->hasCount($where);
        if ($count) {
            return codeRender(Code::SHARE_ACTIVITY_DELETE_EXIST_USER_ERROR);
        }
        $res = $this->repository->deleteRepository($request->input('id'));
        return codeRender(Code::OK, $res);
    }

    public function detail(DetailRequest $request)
    {
        $where = [
            'id' => $request->input('id')
        ];
        $data = $this->repository->detail($where);
        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }
        $data = array_merge($data, app(DistrictService::class)->getDistrictName($data));
        return codeRender(Code::OK, $data);
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];
        $data = $this->repository->list($params);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }
        $code_arr = app(DistrictService::class)->getDistrictCode($data['data']);

        foreach ($data['data'] as $key => &$val) {
            $val['province_name'] = array_get($code_arr, $val['province_code'], '');
            $val['city_name'] = array_get($code_arr, $val['city_code'],'');
            $val['district_name'] = array_get($code_arr, $val['district_code'],'');
        }

        return codeRender(Code::OK, $data);

    }

    /**
     *
     * @api {get} /api/share_activity/apply/list 活动报名列表
     * @apiVersion 1.0.0
     * @apiName ApplyList
     * @apiGroup 运营端-共享空间
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} keyword
     * @apiParam {Number} activity_id 必填
     * @apiParam {Number} per_page
     * @apiParam {Number} page
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Array} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *
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
    "id": 1,
    "activity_id": 1,
    "enterprise_id": 1,
    "user_id": 1,
    "enterprise_name": "ww",
    "user_name": "ee",
    "mobile": "18144355958",
    "created_at": "0",
    "updated_at": "0",
    "deleted_at": null
    }
    ]
    }
    }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function applyList(ApplyListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];
        $data = app(ShareActivityApplyRepository::class)->list($params);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }
        return codeRender(Code::OK, $data);
    }


    private function getEncId(){
        $enc_id = substr(md5(time().rand()), 0, 20);

        $data = $this->repository->getByEncId($enc_id, ['id']);

        if (!empty($data)) {
            return $this->getEncId();
        }
        return $enc_id;
    }
}