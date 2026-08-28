<?php
namespace App\Repositories\Apply;

use App\Criteria\ApplyChart\WhereCommonCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Repositories\CommonRepository;
use App\Repositories\Enterprise\EnterpriseRepository;
use App\Repositories\Policy\MoldRepository;
use App\Repositories\Policy\ProjectRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Models\ApplyModel;

class ApplyChartRepository  extends BaseRepository
{

    use CommonRepository;
	public function model()
	{
		return ApplyModel::class;
	}
	
	/**
	 * 工作台统计
	 * 查询除了草稿之外的全部申请表 ,这里的 arr 中的 group 必传入 否则默认 mold_id
     * 默认是除草稿外的，可以指定在某个环节（包括）后的
	 */
	public function list($arr = [], $apply_staus = 0)
	{
        $where = [];
        if (!empty($arr['start_time'])) {
            $where[] = ['submit_time', '>=', $arr['start_time']];
        }
        if (!empty($arr['end_time'])) {
            $where[] = ['submit_time', '<=', $arr['end_time']];
        }
        if (!empty($arr['enterprise_id'])) {
            $where[] = ['enterprise_id', '=', $arr['enterprise_id']];
        }

        if (!empty($arr['mold_id'])) {
            $where[] = ['mold_id', '=', $arr['mold_id']];
        }

        // 申报总数
        $whereStatus = [['apply_status', '!=', APPLY_STATUS['one']]];
        $column = [
            DB::raw("sum(1) as count"),
            DB::raw("cast(sum(apply_money) as decimal(19, 2)) as money"),
        ];
        $total = $this->model->applyAll()->select($column)->where($where)->where($whereStatus)->first()->toArray();
        $this->resetModel();

        // 受理的数量和钱
        $whereStatusAccept = [['apply_status', '>=', APPLY_STATUS['five']]];
        $column = [
            DB::raw("sum(1) as count"),
            DB::raw("cast(sum(apply_money) as decimal(19, 2)) as money"),
        ];
        $accept = $this->model->applyAll()->select($column)->where($where)->where($whereStatusAccept)->first()->toArray();
        $this->resetModel();

        // 兑现申报
        // 兑现的数量和钱
        $whereStatusSuccess = [['apply_status', '=', APPLY_STATUS['nine']]];
        $column = [
            DB::raw("sum(1) as count"),
            DB::raw("cast(sum(support_content) as decimal(19, 2)) as money"),
        ];
        $support = $this->model->applyAll()->select($column)->where($where)->where($whereStatusSuccess)->get()->toArray();


        $group = 'mold_id';
        $whereStatus = [['apply_status', '>=', APPLY_STATUS['five']]];
        $column = [
            $group,
            DB::raw("sum(1) as count"),
            DB::raw("cast(sum(apply_money) as decimal(19, 2)) as money"),
            DB::raw("cast(sum(support_content) as decimal(19, 2)) as support_money"),
        ];
        $list = $this->model->applyAll()->where($where)->where($whereStatus)->groupBy($group)->get($column)->toArray();


        return [
			'number' => empty($total['count']) ? 0 : $total['count'],
			'money' => empty($total['money']) ? 0 : $total['money'],
			'accept_number' => empty($accept['count']) ? 0 : $accept['count'],
			'accept_money' => empty($accept['money']) ? 0 : $accept['money'],
			'support_number' => empty($support[0]['count']) ? 0 : $support[0]['count'],
			'support_money' => empty($support[0]['money']) ? 0 : $support[0]['money'],
			'list' => empty($list) ? [] : $list
		];
	}
	

	/**
	 * 根据企业ID查询申报信息
	 * select_type : 1 申报记录  2申报信息  3享受支持情况
	 */
	public function getApplyByEnterpriseId($arr = [])
	{
		$selectType = empty($arr['select_type']) ? 1 : $arr['select_type'];
		
		if (empty($arr['enterprise_id']) || !in_array($selectType, [1, 2, 3])) {
			return returnPage([], 0);
		}
		
		$where = [];
		$where[] = ['enterprise_id', '=', $arr['enterprise_id']];
		$columns = ['*'];
		
		if ($selectType == 1) {
			$columns = [
				'id AS apply_id',
				'user_name',
				'policy_name',
				'apply_status',
				'submit_time'
			];
			
			$where[] = ['apply_status', '!=', APPLY_STATUS['one']];
		} else if ($selectType == 2) {
			$columns = [
				'id AS apply_id',
				'business_content',
				'plan_content',
				'approval_organ',
				'approval_number',
				'qualifications',
				'submit_time'
			];
			
			$where[] = ['apply_status', '!=', APPLY_STATUS['one']];
		} else {
			$columns = [
				'id AS apply_id',
				'user_name',
				'policy_name',
				'project_name',
                'apply_money',
				'support_content',
				'allocation_time',
				'submit_time'
			];
			
			$where[] = ['apply_status', '=', APPLY_STATUS['nine']];
		}

		$applyModel = ApplyModel::applyAll()->where($where);

		$count = $applyModel->count();

		$page = commonPage($arr);
		$list = $applyModel
            ->likeProjectMold($arr)
			->orderBy('submit_time', 'desc')
			->offset($page['offset'])
			->limit($page['page_size'])
			->get($columns)
			->toArray();

		return returnPage($list, $count);
	}

    /**
     * FUNCTION_NAME : listByProject
     * author : jp
     *
     * @param array $arr
     * @return array
     */
    public function listByProject($arr = [])
    {
        $where = [];
        if (!empty($arr['start_time'])) {
            $where[] = ['submit_time', '>=', $arr['start_time']];
        }
        if (!empty($arr['end_time'])) {
            $where[] = ['submit_time', '<=', $arr['end_time']];
        }
        $whereStatus = [['apply_status', '!=', APPLY_STATUS['one']]];

        // 这里先查 模糊查询 项目名
        $project_ids = [];
        $project = [];
        if (!empty($arr['keyword'])) {
            $project = app(ProjectRepository::class)->getIdsTrashLike($arr['keyword'], ['id','mold_id','name']);
            $project_ids = array_column($project, 'id');
            $projectCriteria = function ($query) use ( $project_ids) {
                if (!empty($project_ids)) {
                    $query->whereIn('project_id', $project_ids);
                } else {
                    $query->where(DB::raw('1 != 1'));
                }
            };
        } else {
            $projectCriteria = function ($query) use ( $project_ids) {
                if (!empty($project_ids)) {
                    $query->whereIn('project_id', $project_ids);
                }
            };
        }

        $group = 'project_id';
        $column = [
            $group,
            DB::raw("sum(1) as count"),
            DB::raw("cast(sum(apply_money) as decimal(19, 2)) as money"),
            DB::raw("count(distinct(enterprise_id)) as enterprise_count"),
        ];
        $current_page = array_get($arr,'page',1);
        $per_page = isset($arr['per_page']) ? get_per_page($arr['per_page']):env('FRONT_PAGE_SIZE');
        $res = $this->model->applyAll()->where($where)->where($whereStatus)->where($projectCriteria)->groupBy($group)->select($column)->paginate($per_page);
        $list = page($res,$current_page);

        if (empty($list['data'])) {
            return $list;
        }

        if (empty($project)) {
            $project_ids = array_column($list['data'], 'project_id');
            $project = app(ProjectRepository::class)->allForOverviewByIds($project_ids, ['id','mold_id','name']);
        }

        $mold_ids = array_column($project, 'mold_id');

        $mold = app(MoldRepository::class)->getByIds($mold_ids, ['id','name']);
        $mold = array_column($mold, 'name', 'id');
        $project = array_column($project, null, 'id');

        // 受理的申报
        // 受理的数量和钱
        $whereStatusAccept = [['apply_status', '>=', APPLY_STATUS['five']]];
        $column = [
            $group,
            DB::raw("sum(1) as count"),
            DB::raw("cast(sum(apply_money) as decimal(19, 2)) as money"),
        ];
        $accept = $this->model->applyAll()->select($column)->where($where)->where($whereStatusAccept)->groupBy($group)->get()->toArray();

        $accept = array_column($accept, null, $group);

        // 兑现申报
        // 兑现的数量和钱
        $whereStatusAccept = [['apply_status', '=', APPLY_STATUS['nine']]];
        $column = [
            $group,
            DB::raw("sum(1) as count"),
            DB::raw("cast(sum(support_content) as decimal(19, 2)) as money"),
        ];
        $support = $this->model->applyAll()->select($column)->where($where)->where($whereStatusAccept)->groupBy($group)->get()->toArray();
        $support = array_column($support, null, $group);

        // 申报的企业

        foreach ($list['data'] as $key => &$value) {
            $value['project_name'] = empty($project[$value['project_id']]['name']) ? '' : $project[$value['project_id']]['name'];
            $mold_id = empty($project[$value['project_id']]['mold_id']) ? '' : $project[$value['project_id']]['mold_id'];
            $value['mold_id'] = $mold_id;
            $value['policy_name'] = empty($mold[$mold_id]) ? '' : $mold[$mold_id];
            $value['accept_number'] = empty($accept[$value['project_id']]) ? 0 : $accept[$value['project_id']]['count'];
            $value['accept_money'] = empty($accept[$value['project_id']]) ? 0 : $accept[$value['project_id']]['money'];
            $value['support_money'] = empty($support[$value['project_id']]) ? 0 : $support[$value['project_id']]['money'];
        }
        return $list;

    }

    public function overviewByProject($arr)
    {
        $where = [];
        if (!empty($arr['start_time'])) {
            $where[] = ['submit_time', '>=', $arr['start_time']];
        }
        if (!empty($arr['end_time'])) {
            $where[] = ['submit_time', '<=', $arr['end_time']];
        }
        $whereStatus = [['apply_status', '!=', APPLY_STATUS['one']]];

        // 这里先查 模糊查询 项目名
        $project_ids = [];
        $project = [];
        if (!empty($arr['keyword'])) {
            $project = app(ProjectRepository::class)->getIdsTrashLike($arr['keyword'], ['id','mold_id','name']);
            $project_ids = array_column($project, 'id');
            $projectCriteria = function ($query) use ( $project_ids) {
                if (!empty($project_ids)) {
                    $query->whereIn('project_id', $project_ids);
                } else {
                    $query->where(DB::raw('1 != 1'));
                }
            };
        } else {
            $projectCriteria = function ($query) use ( $project_ids) {
                if (!empty($project_ids)) {
                    $query->whereIn('project_id', $project_ids);
                }
            };
        }

        // 总数
        $total = $this->model->applyAll()->where($where)->where($whereStatus)->where($projectCriteria)->count();
        $this->resetModel();
        // 成功申报数
        $whereStatusSuccess = [['apply_status', '=', APPLY_STATUS['nine']]];
        $support_total = $this->model->applyAll()->where($where)->where($whereStatusSuccess)->where($projectCriteria)->count();

        $this->resetModel();
        // 申报的钱 拨款反馈的钱
        $column = [
            DB::raw("cast(sum(apply_money) as decimal(19, 2)) as money"),
            DB::raw("cast(sum(support_content) as decimal(19, 2)) as support_money"),
        ];
        $money = $this->model->applyAll()->where($where)->where($whereStatus)->where($projectCriteria)->get($column)->toArray();

        // 受理的数量和钱
        $whereStatusAccept = [['apply_status', '>=', APPLY_STATUS['five']]];
        $column = [
            DB::raw("sum(1) as count"),
            DB::raw("cast(sum(apply_money) as decimal(19, 2)) as money"),
        ];
        $accept = $this->model->applyAll()->select($column)->where($where)->where($whereStatusAccept)->first()->toArray();


        // 兑现申报
        // 兑现的数量和钱
        $whereStatusSuccess = [['apply_status', '=', APPLY_STATUS['nine']]];
        $column = [
            DB::raw("sum(1) as count"),
            DB::raw("cast(sum(support_content) as decimal(19, 2)) as money"),
        ];
        $support = $this->model->applyAll()->select($column)->where($where)->where($whereStatusSuccess)->get()->toArray();



        return [
            'number' => empty($total) ? 0 : $total,
            'support_number' => empty($support_total) ? 0 : $support_total,
            'money' => empty($money) ? 0 : $money[0]['money'],
            'support_money' => empty($support[0]) ? 0 : $support[0]['money'],
            'accept_number' => empty($accept) ? 0 : $accept['count'],
            'accept_money' => empty($accept) ? 0 : $accept['money'],
        ];
    }

    /**
     * 根据企业ID查询申报信息
     * select_type : 1 申报记录  2申报信息  3享受支持情况
     */
    public function getApplyByEnterpriseIdV2($arr = [])
    {
        $selectType = empty($arr['select_type']) ? 1 : $arr['select_type'];
        $page = commonPageV2($arr);

        if (empty($arr['enterprise_id']) || !in_array($selectType, [1, 2, 3])) {
            return returnPageV3([], 0, $page);
        }

        $where = [];
        $where[] = ['enterprise_id', '=', $arr['enterprise_id']];
        $columns = ['*'];

        if ($selectType == 1) {
            $columns = [
                'id AS apply_id',
                'user_name',
                'policy_name',
                'apply_status',
                'submit_time'
            ];

            $where[] = ['apply_status', '!=', APPLY_STATUS['one']];
        } else if ($selectType == 2) {
            $columns = [
                'id AS apply_id',
                'business_content',
                'plan_content',
                'approval_organ',
                'approval_number',
                'qualifications',
                'submit_time'
            ];

            $where[] = ['apply_status', '!=', APPLY_STATUS['one']];
        } else {
            $columns = [
                'id AS apply_id',
                'user_name',
                'policy_name',
                'project_name',
                'apply_money',
                'support_content',
                'allocation_time',
                'submit_time'
            ];

            $where[] = ['apply_status', '=', APPLY_STATUS['nine']];
        }

        $applyModel = ApplyModel::likeProjectMold($arr)->where($where);

        $count = $applyModel->count();

        $list = $applyModel
            ->orderBy('submit_time', 'desc')
            ->orderBy('id', 'desc')
            ->offset($page['offset'])
            ->limit($page['per_page'])
            ->get($columns)
            ->toArray();

        return returnPageV3($list, $count, $page);
    }

    /**
     * FUNCTION_NAME : listEnterpriseByProject
     * author : jp
     * 统计按项目查询企业
     * @param $arr
     * @return array
     */
    public function listEnterpriseByProject($arr)
    {
        $where = [];
        if (!empty($arr['start_time'])) {
            $where[] = ['submit_time', '>=', $arr['start_time']];
        }
        if (!empty($arr['end_time'])) {
            $where[] = ['submit_time', '<=', $arr['end_time']];
        }
        $whereStatus = [['apply_status', '!=', APPLY_STATUS['one']]];

        $projectCriteria = ['project_id' => $arr['project_id']];

        $group = 'project_id';
        $column = [
            $group,
            'submit_time',
            'user_id',
            'enterprise_id',
            'id'
        ];
        $current_page = array_get($arr,'page',1);
        $per_page = isset($arr['per_page']) ? get_per_page($arr['per_page']):env('FRONT_PAGE_SIZE');
        $keyword = array_get($arr, 'keyword', '');
        if (!blank($keyword)) {
            $res = $this->model->applyAll()->where('enterprise_name', 'like', "%$keyword%");
        } else {
            $res = $this->model->applyAll();

        }

        $res = $res->where($where)->where($whereStatus)->where($projectCriteria)->orderBy('id', 'DESC')->select($column)->paginate($per_page);
        $list = page($res,$current_page);

        if (empty($list['data'])) {
            return $list;
        }

        $ids = array_column($list['data'], 'enterprise_id');
        $enterprise = app(EnterpriseRepository::class)->getByIds($ids,['id','name']);
        $enterprise = array_column($enterprise, null, 'id');
        $userIds = array_column($list['data'], 'user_id');
        $user = app(UserRepository::class)->getByIds($userIds,['id','name', 'mobile'], QUERY_TRASHED);
        $user = array_column($user, null, 'id');


        foreach ($list['data'] as $k => $v) {
            $tmp = array_get($enterprise, $v['enterprise_id']);
            $tmpUser = array_get($user, $v['user_id']);
            $v['enterprise_name'] = array_get($tmp, 'name', '');
            $v['user_name'] = array_get($tmpUser, 'name', '');
            $v['mobile'] = array_get($tmpUser, 'mobile', '');
            $list['data'][$k] = $v;
        }
        return $list;
    }

}
