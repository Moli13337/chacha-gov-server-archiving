<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/12/11
 * Time: 16:27
 */

namespace App\Http\Controllers\Service;


use App\Common\RedisConstant;
use App\Models\ProjectModel;
use App\Models\UserModel;
use App\Repositories\Policy\ProjectIndustryRepository;
use App\Repositories\Policy\ProjectRepository;
use App\Repositories\Steward\StewardPushRecordRepository;
use App\Repositories\User\UserFollowIndustryRepository;
use App\Repositories\User\UserPushRepository;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Redis;
use phpDocumentor\Reflection\Project;

class RecommendedService extends BaseService
{

    protected $recommendedSystemService;
    public function __construct(RecommendedSystemService $recommendedSystemService)
    {
        $this->recommendedSystemService = $recommendedSystemService;
    }

    public function recommend()
    {
        $source = Redis::rpop(RedisConstant::RECOMMEND);
        $this->industry($source);
    }

    public function industry($source)
    {
        $source = $this->getObj($source);
        if (empty($source)) {
            return;
        }
        list($obj_id, $obj_type) = $source;
        $sourceIndustry = $this->getObjIndustry($obj_id, $obj_type);
        $sourceIndustry = $this->filterIndustry($sourceIndustry);

        $data = $this->recommendProject($obj_id, $obj_type, $sourceIndustry);
        $this->recommendSave($data);
    }

    public function recommendProject($obj_id, $obj_type, $item)
    {
        $data = [];
        if ($obj_type == OBJ_TYPE['user']) {
            $project = $this->getProject();
            foreach ($project as $k => $v) {
                 $target = $this->getProjectIndustry($v['id']);
                 $target = $this->filterIndustry($target);
                 $score = $this->recommendedSystemService->jaccard($item, $target);
                 if ($score > 0) {
                        $data[] = [
                            'obj_id' => $v['id'],
                            'user_id' => $obj_id,
                            'obj_type' => OBJ_TYPE['project'],
                        ];
                 }
            }
        } elseif ($obj_type == OBJ_TYPE['project']) {
            $user = $this->getUser();
            foreach ($user as $k => $v) {
                $target = $this->getUserIndustry($v['id']);
                $target = $this->filterIndustry($target);
                $score = $this->recommendedSystemService->jaccard($item, $target);
                if ($score > 0) {
                    $data[] = [
                        'obj_id' => $obj_id,
                        'user_id' => $v['id'],
                        'obj_type' => OBJ_TYPE['project'],
                    ];
                }
            }
        }

        return $data;
    }

    public function recommendSave($data)
    {
        if (empty($data)) {
            return;
        }
        $time = time();
        foreach ($data as $k => $v) {
            $v['created_at'] = $time;
            $v['updated_at'] = $time;
            $data[$k] = $v;
        }
        app(UserPushRepository::class)->storeBatchRepository($data);

    }

    public function getObj($source)
    {
        if (empty($source)) {
            return [];
        }
        $source = explode(',', $source);
        if (empty($source) || count($source) != 2) {
            return [];
        }
        return $source;
    }

    public function getProject()
    {
        $where = [
            'publish_status' => PUBLISH_STATUS['yes'],
        ];
        $data = ProjectModel::select(['id'])->where($where)->get()->toArray();
        return $data;
    }

    public function getUser()
    {
        $where = [
            'is_forbidden' => USER_FORBIDDEN['no'],
        ];
        $data = UserModel::select(['id'])->where($where)->get()->toArray();
        return $data;
    }

    public function getObjIndustry($obj_id, $obj_type)
    {
        if ($obj_type == OBJ_TYPE['user']) {
            return $this->getUserIndustry($obj_id);
        } elseif ($obj_type == OBJ_TYPE['project']) {
            // 这里需要先界定状态是否发布
            $where = [
                'publish_status' => PUBLISH_STATUS['yes'],
                'id' => $obj_id
            ];
            $origin = ProjectModel::select(['id'])->where($where)->first();
            if (empty($origin)) {
                return [];
            }
            return $this->getProjectIndustry($obj_id);
        }
        return [];
    }

    public function getUserIndustry($id)
    {
        $industryItem = [
            'first_industry_id',
            'second_industry_id',
            'third_industry_id',
            'fourth_industry_id',
        ];
        $userColumn = array_merge(['user_id'], $industryItem);
        $user = app(UserFollowIndustryRepository::class)->getAll($id, $userColumn);

        $userIndustry = [];

        foreach ($user as $k => $v) {
            foreach ($industryItem as $vi) {
                $userIndustry[] = array_get($v, $vi, 0);
            }
        }
        return $userIndustry;
    }

    public function getProjectIndustry($id)
    {
        $industryItem = [
            'first_industry_id',
            'second_industry_id',
            'third_industry_id',
            'fourth_industry_id',
        ];
        $projectColumn = array_merge(['project_id'], $industryItem);
        // 申报的行业
        $project = app(ProjectIndustryRepository::class)->list($id, $projectColumn);
        $projectIndustry = [];

        foreach ($project as $k => $v) {
            foreach ($industryItem as $vi) {
                $projectIndustry[] = array_get($v, $vi, 0);
            }
        }

        return $projectIndustry;
    }

    public function filterIndustry($arr)
    {
        return array_unique(array_filter($arr));
    }
}