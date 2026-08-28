<?php
namespace App\Repositories\Apply;

use App\Models\ApprovalModel;
use Exception;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;
use App\Models\ApprovalAcceptModel;
use App\Models\StaffBindDepartmentModel;
use App\Models\StaffDepartmentModel;

class ApprovalAcceptRepository  extends BaseRepository
{

	public function model()
	{
		return ApprovalAcceptModel::class;
	}

	/**
	 * 列表
	 * 今日受理申报记录：此模块只有企业服务中心可以看见
	 */
	public function list($arr = [])
	{
		// 企业服务中心的人员都可以看
		$staff = (new StaffBindDepartmentModel())
			->setTable('f1')
			->from(StaffBindDepartmentModel::TABLE_NAME . ' AS f1')
			->join(StaffDepartmentModel::TABLE_NAME . ' AS f2','f2.id','=','f1.department_id')
			->where([
				'f1.staff_id' => getLoginStaff('id'),
				'f2.type' => DEPARTMENT_TYPE['one']
			])
			->limit(1)
			->get(['f1.department_id'])
			->toArray();
		
		if (empty($staff)) {
			return returnPage([], 0);
		}

		$columns = ['*'];

		$where = [];

		$acceptModel = ApprovalAcceptModel::where($where);

		$count = $acceptModel->count();

		$page = commonPage($arr);

		// 从工作台进来的 需要 按照is_read排序
		if (!empty($arr['is_read']) && $arr['is_read'] == USER_MESSAGE_READ['not']) {
            $acceptModel = $acceptModel->orderBy('is_read', 'ASC');
        }
		$list = $acceptModel
			->orderBy('created_at', 'desc')
			->offset($page['offset'])
			->limit($page['page_size'])
			->get($columns)
			->toArray();

		if (!empty($list)) {
		    $approvalId = array_column($list, 'approval_id');
            $apply = ApprovalModel::whereIn('id', $approvalId)->select(['id', 'apply_id'])->get()->toArray();
            $apply = array_column($apply, 'apply_id', 'id');
            foreach ( $list as $key => $value) {
                $list[$key]['apply_id'] = array_get($apply, $value['approval_id'], 0);
            }
        }

		return returnPage($list, $count);
	}

	/**
	 * 更新
	 */
	public function renew($arr)
	{
		// 先查询是否存在: 因为除了主审部门的操作人员其余也的信息id查询不到
		$accept = ApprovalAcceptModel::where([
				'user_message_id' => $arr['user_message_id'],
			])
			->limit(1)
			->get(['id'])
			->toArray();
		
		if (empty($accept)) {
			return true;
		}

		try{
				
			ApprovalAcceptModel::where([
				'id' => $accept[0]['id']
			])->update([
				'is_read' => USER_MESSAGE_READ['is'],
				'updated_at' => time()
			]);
		
		}catch (Exception $e){
			Log::error('approval accept update' . $e->getMessage());
			return false;
		}
		
		return true;
	}

	/**
	 * 新增
	 */
	public function store($arr)
	{
		$project_name = $arr['project_name'] ?? '';
		$enterprise_name = $arr['enterprise_name'] ?? '';
		$department_name = $arr['department_name'] ?? '';
		
		$content = $enterprise_name.'企业在'.$project_name.'项目的申报已受理，已推送到'.$department_name.'主审部门。';

		$log = [
			'user_message_id' => $arr['user_message_id'],
			'approval_id' => $arr['approval_id'],
			'department_id' => $arr['department_id'],
			'department_name' => $department_name,
			'content' => $content,
			'is_read' => USER_MESSAGE_READ['not'],
			'created_at' => time()
		];

		try{
			
			ApprovalAcceptModel::insert($log);
			
		}catch (Exception $e){
			Log::error('approval accept store' . $e->getMessage());
			return false;
		}
	
		return true;
	}

    /**
     * FUNCTION_NAME : updateRead
     * author : jp
     * 更新已读
     * @param $approval_id
     * @return mixed
     */
	public function updateRead($approval_id)
    {
        $res = ApprovalAcceptModel::where([
            'id' => $approval_id
        ])->update([
            'is_read' => USER_MESSAGE_READ['is'],
            'updated_at' => time()
        ]);
        return $res;
    }
}
