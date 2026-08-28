<?php
namespace App\Repositories\User;

use App\Models\UserModel;
use App\Repositories\BaseRepository;
use App\Models\UserTokenModel;

class UserTokenRepository  extends BaseRepository
{
	public function model()
	{
		return UserTokenModel::class;
	}
	
	/**
	 * 查找token
	 */
	public function findToken($arr)
	{
		$where = [];
		$where[] = ['user_id', '=', $arr['user_id']];
		$where[] = ['type', '=', $arr['type']];
		$where[] = ['sign', '=', $arr['sign']];

		$list = UserTokenModel::where($where)
			->select('created_at', 'updated_at')
			->limit(1)
			->get()
			->toArray();

		if (empty($list)) {
		    return [];
        }
        $result =  $list[0];

        // 当前时间大于token过期时间，token失效
        if (time() > $result['updated_at']) {
            return [];
        }

        // 查询用户信息
        $whereUser = [
            'id' => $arr['user_id']
        ];
        $userList = UserModel::where($whereUser)
            ->limit(1)
            ->get()
            ->toArray();

        if (empty($userList)) {
            return [];
        }

        return array_except($userList[0], ['password', 'deleted_at']);
	}
	
	/**
	 * 新增或更新token
     * type 必传 不传默认 pc
	 */
	public function storeOrUpdateToken($arr)
	{
		$where = [];
		$where[] = ['user_id', '=', $arr['user_id']];
        $type = empty($arr['type']) ? LOGIN_TYPE['pc'] : $arr['type'];
		$where[] = ['type', '=', $type];

		$staffToken = UserTokenModel::where($where)
			->select('id')
			->limit(1)
			->get()
			->toArray();

		// 数据组装
		$expire = $arr['expire'] ?? 604800; // 有效期默认7天
		$currentTime = time();
		$tokenData = [
			'user_id' => $arr['user_id'],
			'type' => $type,
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

	public function resetToken($user_id)
    {
        return $this->model->where('user_id', $user_id)->update(['updated_at' => 0]);
    }
}
