<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/2
 * Time: 18:01
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workbench\ApplyEnterpriseProjectRequest;
use App\Http\Requests\Workbench\ApplyOverviewProjectRequest;
use App\Http\Requests\Workbench\ApplyOverviewRequest;
use App\Repositories\Apply\ApplyChartRepository;
use App\Repositories\Apply\ApprovalAcceptRepository;
use App\Repositories\Policy\MoldRepository;
use App\Repositories\Policy\ProjectRepository;
use App\Support\Collection;
use Illuminate\Http\Request;

class WorkbenchController extends Controller
{

    protected $approvalAcceptRepository;
    protected $applyChartRepository;
    protected $projectRepository;
    protected $moldRepository;

    public function __construct(ApprovalAcceptRepository $approvalAcceptRepository,
                                ApplyChartRepository $applyChartRepository,
                                ProjectRepository $projectRepository,
                                MoldRepository $moldRepository)
    {
        $this->approvalAcceptRepository = $approvalAcceptRepository;
        $this->applyChartRepository = $applyChartRepository;
        $this->projectRepository = $projectRepository;
        $this->moldRepository = $moldRepository;
    }

    /**
     * FUNCTION_NAME : applyAcceptList
     * author : jp
     * 受理审批记录
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function applyAcceptList(Request $request)
    {

        $params = Collection::filter(['per_page', 'page', 'is_read'], $request->all());
        $data = $this->approvalAcceptRepository->list($params);

        return codeRender(Code::OK, $data);
    }

    /**
     * FUNCTION_NAME : applyOverview
     * author : jp
     * 概览
     * @param ApplyOverviewRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function applyOverview(ApplyOverviewRequest $request)
    {
        $params = $this->filter($request);
        $params['start_time'] = empty($params['start_time']) ?
            strtotime(date('Y-m-d'). ' 00:00:00') : $params['start_time'];
        $params['end_time'] = empty($params['end_time']) ?
            time() : $params['end_time'];
        $list = $this->applyChartRepository->list($params, APPLY_STATUS['five']);
        $mold_id = $request->input('mold_id', 0);
        $where = [];
        if ($mold_id) {
            $where[] = ['id', '=', $request->input('mold_id', $mold_id)];
        }
        $mold = $this->moldRepository->whereList($where, ['id','name']);
        $modeChart = [];
        foreach ($mold as $km => $vm) {
            $vm['number'] = 0;
            $vm['money'] = 0;
            $vm['support_money'] = 0;
            $modeChart[$vm['id']] = $vm;
        }
        foreach ($list['list'] as $key => $value) {
            $mold_id = $value['mold_id'];
            $modeChart[$mold_id]['number'] += $value['count'];
            $modeChart[$mold_id]['money'] += $value['money'];
            $modeChart[$mold_id]['support_money'] += $value['support_money'];
        }
        foreach ($modeChart as $k => $v) {
            $modeChart[$k]['money'] = sprintf('%.2f', $v['money']);
            $modeChart[$k]['support_money'] = sprintf('%.2f', $v['support_money']);
        }

        $data = $list;
        unset($data['list']);
        $data['chart'] =  array_values($modeChart);

        return codeRender(Code::OK, $data);
    }

    /**
     *
     * @api GET /api//workbench/applyOverviewListProject  项目申报统计按照项目列表
     * @apiVersion 1.0.0
     * @apiName ApplyOverviewListProject
     * @apiGroup 运营端--统计看板
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} keyword
     * @apiParam {Number} start_time
     * @apiParam {Number} end_time
     * @apiParam {Number} project_id
     * @apiParam {Number} per_page
     * @apiParam {Number} page
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
                    "total": 7,
                    "total_page": 1,
                    "current_page": 1,
                    "per_page_num": 7,
                    "data": [
                            {
                            "project_id": 16,
                            "count": "1",
                            "money": "22.00",
                            "enterprise_count": 1,
                            "project_name": "12fdsafdas3d",
                            "mold_id": 1,
                            "policy_name": "温江区关于促进民营经济健康发展实施意见",
                            "accept_number": "1",
                            "accept_money": "22.00",
                            "support_money": "11.00"
                            },
                    ]
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    /**
     * FUNCTION_NAME : applyOverviewProject
     * author : jp
     * 项目申报统计按照项目列表
     * @param ApplyOverviewProjectRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function applyOverviewListProject(ApplyOverviewProjectRequest $request)
    {
        $params = $this->filter($request);

        $params['start_time'] = empty($params['start_time']) ?
            strtotime(date('Y-m-d'). ' 00:00:00') : $params['start_time'];
        $params['end_time'] = empty($params['end_time']) ?
            time() : $params['end_time'];
        $list = $this->applyChartRepository->listByProject($params);
        return codeRender(Code::OK, $list);
    }

    /**
     * FUNCTION_NAME : applyOverviewProject
     * author : jp
     * 项目申报统计按照项目
     * @param ApplyOverviewProjectRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function applyOverviewProject(ApplyOverviewProjectRequest $request)
    {
        $params = $this->filter($request);

        $params['start_time'] = empty($params['start_time']) ?
            strtotime(date('Y-m-d'). ' 00:00:00') : $params['start_time'];
        $params['end_time'] = empty($params['end_time']) ?
            time() : $params['end_time'];
        $overview = $this->applyChartRepository->overviewByProject($params);
        return codeRender(Code::OK, $overview);
    }

    /**
     *
     * @api GET /api/workbench/applyEnterpriseListProject 项目申报统计按项目--企业列表
     * @apiVersion 1.0.0
     * @apiName ApplyEnterpriseListProject
     * @apiGroup 运营端--统计看板
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {Number} start_time 必填
     * @apiParam {Number} end_time 必填
     * @apiParam {Number} project_id 必填
     * @apiParam {Number} per_page
     * @apiParam {Number} page
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
                    "total": 1,
                    "total_page": 1,
                    "current_page": 1,
                    "per_page_num": 1,
                    "data": [
                            {
                            "project_id": 16,
                            "enterprise_id": 114,
                            "enterprise_name": "成都奇捷电动车配件有限公司",
                            "user_name": "111",
                            "mobile": "13300000000"
                            "submit_time": "1568822400"
                            }
                    ]
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function listEnterpriseByProject(ApplyEnterpriseProjectRequest $request)
    {
        $params = $this->filter($request);

        $params['start_time'] = empty($params['start_time']) ?
            strtotime(date('Y-m-d'). ' 00:00:00') : $params['start_time'];
        $params['end_time'] = empty($params['end_time']) ?
            time() : $params['end_time'];
        $list = $this->applyChartRepository->listEnterpriseByProject($params);
        return codeRender(Code::OK, $list);
    }


}