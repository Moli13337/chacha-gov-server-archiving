<?php
namespace App\Repositories;

use App\Common\Code;
use App\Exceptions\QueryException;
use Prettus\Repository\Eloquent\BaseRepository as CommonRepository;
use Exception;
use Illuminate\Support\Facades\Log;

abstract class BaseRepository extends CommonRepository
{
	
	/**
	 * 新增
	 */
	public function storeRepository($arr)
	{
		$result = [];
		
		try{
			$resultTmp = $this->create($arr);
			$result = [
				'code' => Code::OK,
				'data' => $resultTmp
			];
		}catch (Exception $e){
			// 日志$e->getMessage()
			Log::error('store ' . $e->getMessage());
			
			$result = [
				'code' => Code::DB_ERROR,
				'data' => ''
			];
		}
		
		return $result;
	}

	/**
	 * 删除
	 */
	public function deleteRepository($id)
	{
		$result = [];
		
		try{
			$resultTmp = $this->delete($id);
			$result = [
				'code' => Code::OK,
				'data' => $resultTmp
			];
		}catch (Exception $e){
			// 日志$e->getMessage()
			Log::error('delete ' . $e->getMessage());
			
			$result = [
				'code' => Code::DB_ERROR,
				'data' => ''
			];
		}
		
		return $result;
	}
	
	/**
	 * 更新
	 */
	public function updateRepository($arr)
	{
		$result = [];
		
		try{
			$resultTmp = $this->update(array_except($arr,['id']), $arr['id']);
			$result = [
				'code' => Code::OK,
				'data' => $resultTmp->toArray()
			];
		}catch (Exception $e){
			// 日志$e->getMessage()
			Log::error('update ' . $e->getMessage());
			
			$result = [
				'code' => Code::DB_ERROR,
				'data' => ''
			];
		}
		
		return $result;
	}

    public function findRepository($id)
    {
        return $this->model->find($id);
    }

    public function resetSelectColumn($column = ['*'], $table = '') {
	    if (empty($table)) {
	        return $column;
        } elseif ($column == ['*']) {
            return [$table.'*'];
        } elseif (is_array($column)) {
            foreach ($column as $key => $value) {
                $column[$key] = $table.'.'.$value;
            }
            return $column;
        }

    }

    public function newStoreRepository($input_data)
    {
        try {
            $res = $this->create($input_data);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }
        return $res;
    }

}

