<?php
namespace App\Repositories\Apply;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;
use App\Models\ApprovalConfigModel;
use Illuminate\Support\Facades\Cache;

class ApprovalConfigRepository  extends BaseRepository
{

	public function model()
	{
		return ApprovalConfigModel::class;
	}

	/**
	 * 列表
	 */
	public function configList($arr, $columns = ['*'])
	{
		$list = ApprovalConfigModel::get($columns)->toArray();
		return returnPage($list, count($list));
	}

	/**
	 * 新增
	 */
	public function configUpdate($arr)
	{
		$configList = $arr['config_list'];
		
		DB::beginTransaction();
		
		try{
			
			foreach ($configList as $key => $value) {
				ApprovalConfigModel::where([
					'id' => $value['id']
				])->update([
					'config_value' => $value['config_value']
				]);
			}
			
			// 清除配置缓存
			Cache::forget(CACHE_APPROVAL_CONFIG);

			DB::commit();
			return true;
		
		}catch (Exception $e){
			Log::error('approval config store' . $e->getMessage());
			DB::rollBack();
			return false;
		}
	}
}