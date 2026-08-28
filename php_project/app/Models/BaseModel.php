<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
	// 默认新增编辑时间：created_at 和 updated_at
	public $timestamps = TRUE;
	
	// 时间设置为 Unix 时间戳
	protected $dateFormat = 'U';

	/******事务封装开始*****/
	/**
	 *  手动开启事务
	 */
	public function common_trans_begin() {
		DB::beginTransaction();
	}

	/**
	 *  手动回滚事务
	 */
	public function common_trans_rollback() {
		DB::rollBack();
	}
	
	/**
	 *  手动提交事务
	 */
	public function common_trans_commit() {
		DB::commit();
	}
	
	/******事务封装结束*****/

    public function getDateFormat()
    {
        return 'U';
    }

}