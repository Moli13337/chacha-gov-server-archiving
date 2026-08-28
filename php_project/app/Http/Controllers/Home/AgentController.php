<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/23
 * Time: 15:48
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\DistrictService;
use App\Http\Requests\Agent\Home\CreditListRequest;
use App\Http\Requests\Agent\Home\DetailRequest;
use App\Http\Requests\Agent\Home\ListRequest;
use App\Repositories\Agent\AgentCommentRepository;
use App\Repositories\Agent\AgentRepository;
use Illuminate\Http\Request;

class AgentController extends Controller
{

    protected $agentRepository;

    public function __construct(AgentRepository $agentRepository)
    {
        $this->agentRepository = $agentRepository;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        if (!empty($params['type_id'])) {
            $params['type_id'] = explode(',', $params['type_id']);
        }
        $params['publish_status'] = PUBLISH_STATUS['yes'];

        if ($request->input('is_excellent', 0) == 1) {
            $params['order_by']['composite_stars'] = 'DESC';
            $params['per_page'] = 3;
        }
        $params['order_by']['submit_time'] = 'DESC';


        $column = ['id', 'enc_id','enterprise_id', 'type_id', 'service_item', 'file_name', 'file_url', 'service_detail',
            'province_code','city_code', 'district_code',
            'address',
            'contact_name',
            'contact_phone',
            'publish_status',
            'remark',
            'composite_stars',
            'department_stars',
            'enterprise_stars',
            'created_at',
            'credit_type',

        ];
        $params = app(DistrictService::class)->clientDistrictFilterV2($params);

        $res = $this->agentRepository->clientList($params, $column);
        if (empty($res['data'])) {
            return codeRender(Code::OK, $res);
        }
        $code_arr = app(DistrictService::class)->getDistrictCode($res['data']);

        foreach ($res['data'] as $key => &$value) {
            $value['agent_name'] = array_get($value['enterprise'], 'name', '');
            unset($value['enterprise']);
            $value['agent_type_name'] = array_get($value['agent_type'], 'name', '');
            $value['composite_stars'] = (float)$value['composite_stars'];
            $value['department_stars'] = (float)$value['department_stars'];
            $value['province_name'] = array_get($code_arr, $value['province_code'], '');
            $value['city_name'] = array_get($code_arr, $value['city_code'],'');
            $value['district_name'] = array_get($code_arr, $value['district_code'],'');
            unset($value['agent_type']);
            unset($value['enterprise_id']);

        }
        return codeRender(Code::OK, $res);
    }

    public function detail(DetailRequest $request)
    {
        $column = ['id', 'enc_id','enterprise_id', 'type_id', 'service_item', 'file_name', 'file_url', 'service_detail',
            'province_code','city_code', 'district_code',
            'address',
            'contact_name',
            'contact_phone',
            'publish_status',
            'remark',
            'composite_stars',
            'department_stars',
            'enterprise_stars',
            'created_at',
            'credit_type',
        ];
        $where = [
            'enc_id' => $request->input('id'),
            'publish_status' => PUBLISH_STATUS['yes']
        ];
        $data = $this->agentRepository->detail($where, $column);
        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }
        $data = array_merge($data, app(DistrictService::class)->getDistrictName($data));
        $data['agent_name'] = array_get($data['enterprise'], 'name', '');
        $data['agent_type_name'] = array_get($data['agent_type'], 'name', '');
        $data['composite_stars'] = (float)$data['composite_stars'];
        $data['department_stars'] = (float)$data['department_stars'];
        $data['enterprise_stars'] = (float)$data['enterprise_stars'];

        unset($data['enterprise']);
        unset($data['agent_type']);

        // 这里要统计星
        $data['enterprise_stars_arr'] = app(AgentCommentRepository::class)->getNumGroupType($data['id'],MESSAGE_USER_TYPE['user'],IS_SHOW['yes']);
        $data['department_stars_arr'] = app(AgentCommentRepository::class)->getNumGroupType($data['id'],MESSAGE_USER_TYPE['staff'],IS_SHOW['yes']);

        return codeRender(Code::OK, $data);

    }

    public function credit(CreditListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by']['id'] = 'DESC';
        if ($params['credit_type'] == AGENT_CREDIT_TYPE['general']) {
            $params['publish_status'] = PUBLISH_STATUS['yes'];
        }
        $column = ['id', 'enc_id','enterprise_id', 'type_id', 'publish_status','credit_type',
        ];
        $res = $this->agentRepository->creditList($params, $column);
        if (empty($res['data'])) {
            return codeRender(Code::OK, $res);
        }

        foreach ($res['data'] as $key => &$value) {
            $value['id'] = $value['enc_id'];
            $value['agent_name'] = array_get($value['enterprise'], 'name', '');
            unset($value['enterprise']);
            $value['agent_type_name'] = array_get($value['agent_type'], 'name', '');
            unset($value['agent_type']);
            unset($value['enterprise_id']);
            $value['created_at'] = array_get($value['credit'][0]??[], 'created_at', 0);
            unset($value['credit']);


        }
        return codeRender(Code::OK, $res);
    }
}