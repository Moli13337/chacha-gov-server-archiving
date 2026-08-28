<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/5
 * Time: 0:00
 */

namespace App\Repositories;


use App\Common\Code;
use App\Exceptions\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Exceptions\RepositoryException;

trait CommonRepository
{

    public function storeRepository($input_data)
    {
        try {
            $res = $this->create($input_data);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }
        return $res;
    }

    public function deleteRepository($id)
    {
        try {
            $res = $this->model->destroy($id);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }  catch (ModelNotFoundException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }
        return $res;
    }

    public function updateRepository($input_data)
    {
        try {
            if (!isset($input_data['id'])) {
                return codeRender(Code::PARAM_ERROR, [trans('error.HAS_NOT_ID')]);
            }
            $res = $this->update(array_except($input_data, ['id']), $input_data['id']);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (ModelNotFoundException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }
        return $res;
    }


    public function findRepository($id, $column = ['*'])
    {
        return $this->model->select($column)->find($id);
    }

    public function storeBatchRepository($data)
    {
        try {
            $this->model->insert($data);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        return true;
    }

    public function updateBatchRepository($table, $multipleData = [], $index,$where = [])
    {
        if (empty($table) || empty($multipleData)) {
            return false;
        }

        $column = array_keys($multipleData[0]);

        $whereIn = '';
        $q = "UPDATE `$table` SET ";
        foreach ($column as $item) {
            if ($item == $index) {
                continue;
            }
            $q .= " `$item` = CASE";

            foreach ($multipleData as $value) {
                $q .= " WHEN `$index` = " . $value[$index] . " THEN " . $value[$item];
            }
            $q.= " ELSE `$item` END, ";
        }

        $whereIn = array_column($multipleData, $index);
        $whereIn = implode(',', $whereIn);

        $q = rtrim($q, ', '). " WHERE `$index` IN ($whereIn) ";

        foreach ($where as $k => $v) {
            $q .= " AND `$k` = $v ";
        }
        return DB::update(DB::raw($q));
    }
}

