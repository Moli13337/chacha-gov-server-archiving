<?php
namespace App\Repositories\Staff;

use App\Models\StaffTokenModel;
use App\Repositories\BaseRepository;
use App\Models\StaffModel;

class StaffTokenRepository  extends BaseRepository
{
	public function model()
	{
		return StaffTokenModel::class;
	}
	
	/**
	 * 查找token
	 */
	public function findToken($arr)
	{
		$where = [];
		$where[] = ['staff_id', '=', $arr['staff_id']];
		$where[] = ['sign', '=', $arr['sign']];

		$list = StaffTokenModel::where($where)
			->limit(1)
			->get(['created_at', 'updated_at'])
			->toArray();
		
		if (empty($list)) {
			return [];
		}
		
		$result =  $list[0];

		// 查询用户信息
		$whereStaff = [
			'id' => $arr['staff_id']
		];
		$staffList = StaffModel::where($whereStaff)
			->limit(1)
			->get()
			->toArray();
		
		if (empty($staffList)) {
			return [];
		}
		
		$return = array_except($staffList[0], ['password', 'deleted_at', 'created_at', 'updated_at']);
		$return['created_at'] = $result['created_at'];
		$return['updated_at'] = $result['updated_at'];
		
		return $return;
	}
	
	/**
	 * 新增或更新token
	 */
	public function storeOrUpdateToken($arr)
	{
		$where = [];
		$where[] = ['staff_id', '=', $arr['staff_id']];

		$staffToken = StaffTokenModel::where($where)
			->limit(1)
			->get(['id'])
			->toArray();

		// 数据组装
		$expire = $arr['expire'] ?? 2592000; // 有效期默认1个月
		$currentTime = time();
		$tokenData = [
			'staff_id' => $arr['staff_id'],
			'sign' => $arr['sign'],
			'created_at' => $currentTime,
			'updated_at' => $currentTime + $expire
		];
		
		if (empty($staffToken)) {
			// 新增
			$resultToken = $this->storeRepository($tokenData);
			return $resultToken;
		}
		
		// 更新
		$tokenData['id'] = $staffToken[0]['id'];
		$resultToken = $this->updateRepository($tokenData);
		return $resultToken;
	}
	
	/**
	 * 权限发生变更了处理token即可
	 */
	public function changeToken($staffIds)
	{
		if (!is_array($staffIds)) {
			return true;
		}

		$result = StaffTokenModel::whereIn('staff_id', $staffIds)->update([
			'updated_at' => 0
		]);
		return $result;
	}
}
