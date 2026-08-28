<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/4
 * Time: 16:44
 */

namespace App\Http\Controllers\Admin\Steward;


use App\Common\Code;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\DistrictService;
use App\Http\Controllers\Service\IndustryService;
use App\Http\Requests\StewardPush\DetailRequest;
use App\Http\Requests\StewardPush\IndustryListRequest;
use App\Http\Requests\StewardPush\ListRequest;
use App\Http\Requests\StewardPush\RecordListRequest;
use App\Http\Requests\StewardPush\SaveRequest;
use App\Models\ProjectModel;
use App\Models\Steward\StewardInformationModel;
use App\Repositories\Steward\StewardPushRecordRepository;
use App\Repositories\Steward\StewardPushRepository;
use App\Repositories\User\UserPushRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\DB;

class StewardPushController extends Controller
{

    private $repository;
    private $stewardPushRecordRepository;

    public function __construct(StewardPushRepository $repository, StewardPushRecordRepository $stewardPushRecordRepository)
    {
        $this->repository = $repository;
        $this->stewardPushRecordRepository = $stewardPushRecordRepository;
    }

    public function save(SaveRequest $request)
    {
        $fileColumn = [
            'touser',
            'touser.*.user_id',
            'touser.*.enterprise_id',
            'touser.*.mobile',
            'touser.*.enterprise_name',
            'touser.*.user_name',
        ];

        $white = array_diff(array_keys($request->rules()), $fileColumn);

        // obj_type
        $obj = [];
        if ($request->input('obj_type') == OBJ_TYPE['project']) {
            $obj = ProjectModel::select(['publish_status'])->where(['id' => $request->input('obj_id')])->first();
        } elseif ($request->input('obj_type') == OBJ_TYPE['information_industry'] ||
        $request->input('obj_type') == OBJ_TYPE['information_meeting']) {
            $obj = StewardInformationModel::select(['publish_status'])->where(['id' => $request->input('obj_id')])->first();
        }

        if (empty($obj)) {
            return codeRender(Code::STEWARD_PUSH_OBJ_ERROR);
        } elseif ($obj['publish_status'] != PUBLISH_STATUS['yes']) {
            return codeRender(Code::STEWARD_PUSH_OBJ_STATUS_ERROR);
        }

        $params = $request->only($white);
        $params = $this->initValue($params);
        $params[CREATED_STAFF_ID] = (int)getLoginStaff('id');
        $params['sms_content'] = $this->buildSimpleContent($params['title'], $params['obj_type']);

        $toUser = [];
        list($params['number'], $toUser) = $this->selectUser($params['type'], $request->input('touser', []));

        if (empty($toUser)) {
            return codeRender(Code::STEWARD_PUSH_USER_EMPTY_ERROR);
        }

        try {
            DB::beginTransaction();
            $res = $this->repository->storeRepository($params);
            $this->storeToUser($request, $res['id'], $toUser);
            $this->storePush($request->input('obj_id'), $request->input('obj_type'), $toUser);
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::FAIL, $e->getMessage());
        }
        return codeRender(Code::OK);

    }

    /**
     * FUNCTION_NAME : selectUser
     * author : jp
     * 获取用户
     * @param $type
     * @param array $user
     * @return array
     */
    public function selectUser($type, $user = [])
    {
        $toUser = [];
        switch ($type) {
            case STEWARD_PUSH_TYPE['industry']:
                $toUser = $user;
                break;
            case STEWARD_PUSH_TYPE['authentication']:
                $toUser = $this->selectAuthentication();
                break;
            case STEWARD_PUSH_TYPE['register']:
                $toUser = app(UserRepository::class)->getRegister(['is_forbidden' => USER_FORBIDDEN['no']], ['id as user_id', 'name as user_name','mobile']);
                break;
            default:
                break;
        }
        $number = count($toUser);
        return [$number, $toUser];
    }

    /**
     * FUNCTION_NAME : selectAuthentication
     * author : jp
     * 获取认证用户
     * @return mixed
     */
    public function selectAuthentication()
    {
        $toUser = app(UserRepository::class)->getAuthentication(['is_forbidden' => USER_FORBIDDEN['no']], ['id','name as user_name', 'mobile']);
        $column = [
            'user_id',
            'user_name',
            'mobile',
            'enterprise_id',
            'enterprise_name',
        ];
        foreach ($toUser as $key => &$value) {
            $value['user_id'] = $value['id'];
            $value['enterprise_id'] = array_get($value['enterprise'][0], 'id', 0);
            $value['enterprise_name'] = array_get($value['enterprise'][0], 'name', '');

            $value = array_only($value, $column);

        }
        return $toUser;
    }

    public function storeToUser($request, $id, $toUser = [])
    {

        if (empty($toUser) ) {
            return false;
        }
        $white = [
            'user_id',
            'enterprise_id',
            'mobile',
            'enterprise_name',
            'user_name',
        ];
        $timeArr = returnCreatedUpdatedAt();

        foreach ($toUser as $key => $value) {
            $append = [
                'steward_push_id' => $id,
                'content' => $this->buildContent($request->input('obj_title'), $request->input('obj_type'), array_get($value, 'enterprise_name', ''))
            ];
            $toUser[$key] = array_merge(array_only($value, $white), $append, $timeArr);
        }
        return $this->stewardPushRecordRepository->storeBatchRepository($toUser);
    }

    public function storePush($obj_id, $obj_type, $toUser = [])
    {
        if (empty($toUser) ) {
            return false;
        }
        $data = [];
        $time = time();
        foreach ($toUser as $k => $v) {
            $data[] = [
                'obj_id' => $obj_id,
                'obj_type' => $obj_type,
                'user_id' => $v['user_id']??0,
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }

        if (!empty($data)) {
            app(UserPushRepository::class)->storeBatchRepository($data);
        }
    }

    /**
     * FUNCTION_NAME : buildSimpleContent
     * author : jp
     * 生成发送信息的主要信息
     * @param $title
     * @param $type
     * @return array|\Illuminate\Contracts\Translation\Translator|string|null
     */
    private function buildSimpleContent($title, $type)
    {
        $tmp  = [
            'title' => $title
        ];
        switch ($type) {
            case STEWARD_PUSH_OBJ_TYPE['project']:
                $content =  trans('messageMain.steward_push_project', $tmp);
                break;
            case STEWARD_PUSH_OBJ_TYPE['industry']:
                $content = trans('messageMain.steward_push_information_industry', $tmp);
                break;
            case STEWARD_PUSH_OBJ_TYPE['meeting']:
                $content = trans('messageMain.steward_push_information_meeting', $tmp);
                break;
            default:
                $content =  '';
        }

        return $content;
    }

    /**
     * FUNCTION_NAME : buildContent
     * author : jp
     * 生成发送信息
     * @param $title
     * @param $type
     * @param $name
     * @return array|\Illuminate\Contracts\Translation\Translator|string|null
     */
    private function buildContent($title, $type, $name)
    {
        $tmp  = [
            'title' => $title,
            'enterprise_name' => $name,
        ];
        switch ($type) {
            case STEWARD_PUSH_OBJ_TYPE['project']:
                $content =  trans('message.steward_push_project', $tmp);
                break;
            case STEWARD_PUSH_OBJ_TYPE['industry']:
                $content = trans('message.steward_push_information_industry', $tmp);
                break;
            case STEWARD_PUSH_OBJ_TYPE['meeting']:
                $content = trans('message.steward_push_information_meeting', $tmp);
                break;
            default:
                $content =  '';
        }

        return $content;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];
        $data = $this->repository->list($params);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['created_staff_name'] = array_get($value['staff'], 'name', '');
            unset($data['data'][$key]['staff']);
        }
        $data['data'] = app(IndustryService::class)->getIndustryNameList($data['data']);
        return codeRender(Code::OK, $data);
    }

    public function detail(DetailRequest $request)
    {
        $where = [
            'id' => $request->input('id')
        ];
        $detail = $this->repository->detail($where);
        if (empty($detail)) {
            return codeRender(Code::OK, []);

        }
        $industry_name = app(IndustryService::class)->getIndustryName($detail);
        $detail = array_merge($detail, $industry_name);

        return codeRender(Code::OK, $detail);
    }

    public function industryList(IndustryListRequest $request)
    {
        $params = $this->filter($request);
        $params = $this->initValue($params);
        $params['order_by'] = ['id' => 'DESC'];
        $params['is_forbidden'] = USER_FORBIDDEN['no'];
        $column = ['id', 'name', 'mobile'];
        $data = app(UserRepository::class)->getUserByFollowIndustry($params,$column);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        $column = [
            'user_id',
            'user_name',
            'mobile',
            'enterprise_id',
            'enterprise_name',
        ];

        $industry = app(IndustryService::class)->getIndustryName($params);
        foreach ($data['data'] as $key => &$value) {
            $value['user_id'] = $value['id'];
            $value['user_name'] = $value['name'];
            $value['enterprise_id'] = array_get($value['enterprise'][0], 'id', 0);
            $value['enterprise_name'] = array_get($value['enterprise'][0], 'name', '');

            $value = array_merge(array_only($value, $column), $industry);
        }
        return codeRender(Code::OK, $data);
    }

    public function record(RecordListRequest $request)
    {
        $params = $this->filter($request);
        $params['order_by'] = ['id' => 'DESC'];
        $where = [
            'id' => $request->input('steward_push_id'),
            'type' => STEWARD_PUSH_TYPE['industry'],
        ];
        $detail = $this->repository->detail($where);
        if (empty($detail)) {
            return codeRender(Code::OK, []);
        }
        $industry_name = app(IndustryService::class)->getIndustryName($detail);

        $data = $this->stewardPushRecordRepository->list($params);
        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);

        }
        foreach ($data['data'] as $key => &$value) {
            $value = array_merge($value, $industry_name);
        }
        return codeRender(Code::OK, $data);
    }

    public function initValue($data)
    {
        $keys = [
            "first_industry_id" => 0,
            "second_industry_id" => 0,
            "third_industry_id" => 0,
            "fourth_industry_id" => 0,
        ];
        foreach ($keys as $key => $value) {
            $data[$key] = empty($data[$key]) ? $value : $data[$key];
        }
        return $data;
    }



}