<?php
namespace App\Repositories\User;

use App\Common\Code;
use App\Models\StaffCodeModel;
use App\Models\UserCodeModel;
use App\Repositories\BaseRepository;

class UserCodeRepository  extends BaseRepository
{
	public function model()
	{
		return UserCodeModel::class;
	}
	
	/**
	 * 校验验证码 
	 */
	public function checkCode($arr)
	{
		$time = time() - $arr['expire'];
		$where = [];
		$where[] = ['mobile', '=', $arr['mobile']];
		$where[] = ['created_at', '>=', $time];

		$list = UserCodeModel::where($where)
			->select('code')
			->orderBy('created_at', 'desc')
			->limit(1)
			->get()
			->toArray();
		
		$result = empty($list) ? [] : $list[0];
		return $result;
	}
}
