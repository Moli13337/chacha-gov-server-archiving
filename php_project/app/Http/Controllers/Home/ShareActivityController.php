<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/7
 * Time: 15:42
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\DistrictService;
use App\Http\Requests\ShareActivity\HomeDetailRequest;
use App\Http\Requests\ShareActivity\HomeListRequest;
use App\Models\Share\ShareActivityModel;
use App\Repositories\Share\ShareActivityApplyRepository;
use App\Repositories\Share\ShareActivityRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

class ShareActivityController extends Controller
{

    protected $repository;
    public function __construct(ShareActivityRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list(HomeListRequest $request)
    {
        $params = $this->filter($request);
        $params = array_merge($params, $this->selectTime($request));
        $params['order_by'] = ['id' => 'DESC'];
        $params['publish_status'] = PUBLISH_STATUS['yes'];
        $column = [
            'id',
            'enc_id',
            'title',
            'content',
            'number',
            'province_code',
            'city_code',
            'district_code',
            'address',
            'sponsor',
            'type',
            'status',
            'publish_status',
            'publish_time',
            'validity_sdate',
            'validity_edate',
            'file_name',
            'file_url',
            'mobile',
            'status_name'
        ];
        $data = $this->repository->clientList($params,$column);
        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        $code_arr = app(DistrictService::class)->getDistrictCode($data['data']);

        $column[] = 'apply_count';
        $column[] = 'type_name';
        foreach ($data['data'] as $key => $val) {
            $tmp = array_only($val, $column);
            $tmp['id'] = $val['enc_id'];
            $tmp['province_name'] = array_get($code_arr, $val['province_code'], '');
            $tmp['city_name'] = array_get($code_arr, $val['city_code'],'');
            $tmp['district_name'] = array_get($code_arr, $val['district_code'],'');
            $data['data'][$key] = $tmp;
        }
        return codeRender(Code::OK, $data);
    }

    public function selectTime($request)
    {
        if (empty($request->input('selectTime'))) {
            return [];
        }

        $time = [];
        $today = date('Y-m-d');
        $tmp = strtotime($today);
        switch ($request->input('selectTime')) {
            case 1: // 今天
                $time['start_time'] = $tmp;
                $time['end_time'] = strtotime('+1 days', $tmp) - 1;
                break;
            case 2: // 明天
                $time['start_time'] = strtotime('+1 days', $tmp);
                $time['end_time'] = strtotime('+2 days', $tmp) - 1;
                break;
            case 3: // 本周
                $time['start_time'] = mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"));
                $time['end_time'] = mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y"));
                break;
            case 4: // 本月
                $time['start_time'] = mktime(0, 0 , 0,date("m"),1,date("Y"));
                $time['end_time'] = mktime(23,59,59,date("m"),date("t"),date("Y"));
                break;
            default:
                break;
        }

        return $time;
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
            'number',
            'province_code',
            'city_code',
            'district_code',
            'address',
            'sponsor',
            'type',
            'publish_status',
            'publish_time',
            'validity_sdate',
            'validity_edate',
            'file_name',
            'file_url',
            'mobile',
        ];
        $data = $this->repository->clientDetail($where,$column);
        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }
        $data['id'] = $data['enc_id'];
        $data = array_merge($data, app(DistrictService::class)->getDistrictName($data));
        return codeRender(Code::OK, $data);
    }

    /**
     * FUNCTION_NAME : submit
     * author : jp
     * 报名
     * @param HomeDetailRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \App\Exceptions\QueryException
     */
    public function submit(HomeDetailRequest $request)
    {
        $user_id = (int)getLoginHome('id');
        $enterprise = app(UserRepository::class)->enterpriseDetail($user_id);
        $where = [
            'enc_id' => $request->input('id'),
        ];
        $detail = $this->repository->detail($where, ['id', 'status', 'number','publish_status']);
        if (empty($detail)) {
            return codeRender(Code::PARAM_ERROR, []);
        } elseif ($detail['publish_status'] == PUBLISH_STATUS['no']) {
            return codeRender(Code::SHARE_ACTIVITY_NOT_PUBLISH_STATUS);
        } elseif ($detail['status'] == SHARE_ACTIVITY_STATUS['off']) {
            return codeRender(Code::SHARE_ACTIVITY_OFF);
        } elseif ($detail['status'] == SHARE_ACTIVITY_STATUS['over']) {
            return codeRender(Code::SHARE_ACTIVITY_OVER);
        }
        $where = [
            'user_id' => $user_id,
            'activity_id' => $detail['id'],
        ];
        $count = app(ShareActivityApplyRepository::class)->hasCount($where);
        if ($count) {
            return codeRender(Code::SHARE_ACTIVITY_SUBMIT_REPEAT);
        }

        $where = [
            'activity_id' => $detail['id'],
        ];
        $count = app(ShareActivityApplyRepository::class)->hasCount($where);
        if ($count >= $detail['number']) {
            return codeRender(Code::SHARE_ACTIVITY_SUBMIT_FULL);
        }

        $store = [
            'user_id' => $user_id,
            'user_name' => (string)getLoginHome('name'),
            'mobile' => (string)getLoginHome('mobile'),
            'enterprise_id' => array_get($enterprise, 'id', 0),
            'enterprise_name' => array_get($enterprise, 'name', ''),
            'activity_id' => array_get($detail, 'id', ''),
        ];

        app(ShareActivityApplyRepository::class)->storeRepository($store);

        return codeRender(Code::OK, []);
    }


}