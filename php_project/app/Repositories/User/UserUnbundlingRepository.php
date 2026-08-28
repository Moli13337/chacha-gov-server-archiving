<?php
namespace App\Repositories\User;


use App\Models\UserUnbundlingModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class UserUnbundlingRepository  extends BaseRepository
{
    use CommonRepository;
	public function model()
	{
		return UserUnbundlingModel::class;
	}

	public function lastFirst($user_id)
    {
        $where = [
            'user_id' => $user_id,
            'step' => UNBUNDLING_STEP_FIRST,
        ];
        $time = time() - 10*60;
        $res = $this->model->where($where)->where('created_at', '>', $time)->orderBy('id',  'DESC')->first();
        return $res;
    }
	

}
