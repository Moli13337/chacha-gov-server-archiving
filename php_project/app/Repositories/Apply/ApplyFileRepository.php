<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/2
 * Time: 18:27
 */

namespace App\Repositories\Apply;


use App\Common\Code;
use App\Criteria\ApplyChart\SupplementCriteria;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\WhereCreatedEndCriteria;
use App\Criteria\WhereCreatedStartCriteria;
use App\Exceptions\QueryException;
use App\Models\ApplyFileExceptionModel;
use App\Models\ApplyFileModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class ApplyFileRepository extends BaseRepository
{

    use CommonRepository;

    public function model()
    {
        return ApplyFileModel::class;
    }

    /**
     * FUNCTION_NAME : refreshInvoice
     * author : jp
     * 清除发票的识别信息
     * @param $apply_id
     * @return mixed
     */
    public function refreshInvoice($apply_id)
    {
        $update = [
            'invoice_number' => '',
            'invoice_money' => '',
            'invoice_billing_date' => '',
            'invoice_checkcode' => '',
            'invoice_code' => '',
            'check_status' => APPLY_CHECK_STATUS['init'],
        ];
        $where = [
            'apply_id' => $apply_id,
            'file_type' => MATERIALS_TYPE['invoice'],
        ];
        return $this->model->where($where)->update($update);
    }

    public function getByApply($apply_id, $column = ['*'])
    {
        return $this->model->select($column)->where('apply_id',$apply_id)->get()->toArray();
    }

    public function supplementList($search_arr, $column= ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        $where = [];
        $where[] = ['f2.apply_id', '=', $search_arr['apply_id']];

        $where[] = ['f2.file_type', '=', MATERIALS_TYPE['invoice']];

        $whereColumn = [
            'ocr',
            'repeat',
        ];

        foreach ($whereColumn as $value) {
            if (!empty($search_arr[$value])) {
                $where[] = ['f1.'.$value, '=', $search_arr[$value]];
            }
        }

        $keyword = trim(array_get($search_arr, 'keyword'));
        $func = [];
        if (!blank($keyword)) {
            $keyword = "%$keyword%";
            $func = function ($query) use ($keyword){
                $query->where('f2.file_name', 'like', $keyword);
                $query->orWhere('f2.invoice_number', 'like', $keyword);
            };
        }

        $column = [
            'f2.id AS file_id',
            'f2.apply_id',
            'f2.file_name',
            'f2.file_url',
            'f2.invoice_number',
            'f2.invoice_money',
            'f2.invoice_billing_date',
            'f2.invoice_checkcode',
            'f2.invoice_code',
            'f1.remark',
            'f1.ocr',
            'f1.is_year',
            'f1.is_truth',
            'f1.repeat_apply',
            'f1.repeat',
            'f1.created_at'
        ];
        $res = (new ApplyFileModel())
            ->setTable('f2')
            ->from(ApplyFileModel::TABLE_NAME . ' AS f2')
            ->leftJoin(ApplyFileExceptionModel::TABLE_NAME . ' AS f1','f1.apply_file_id','=','f2.id')
            ->where($where)
            ->where($func)
            ->whereNull('f1.deleted_at')
            ->whereNull('f2.deleted_at')
            ->paginate($per_page, $column);
        $data = page($res, $current_page);
        // 应前端要求多加一个invoice
        if (empty($data['data'])) {
            return $data;
        }
        foreach ($data['data'] as $k => &$v) {
            $v['invoice'] = [
                'invoice_number' => $v['invoice_number'],
                'invoice_money' => $v['invoice_money'],
                'invoice_billing_date' => $v['invoice_billing_date'],
                'invoice_checkcode' => $v['invoice_checkcode'],
                'invoice_code' => $v['invoice_code'],
            ];
            $v['ocr'] = $v['ocr']??0;
            $v['repeat'] = $v['repeat']??0;
            $v['ocr'] = $v['ocr']??0;
            $v['repeat'] = $v['repeat']??0;
            $v['is_truth'] = $v['is_truth']??0;
            $v['is_year'] = $v['is_year']??0;
            $v['repeat_apply'] = $v['repeat_apply']??0;
            $v['remark'] = $v['remark']??'';
        }

        return $data;
    }

    public function deleteByApply($id)
    {
        return $this->model->where('apply_id', $id)->delete();
    }

    public function deleteByIdsAndApply($applyId, $ids)
    {
        return $this->model->where('apply_id', $applyId)->whereIn('id', $ids)->delete();
    }


    /**
     * FUNCTION_NAME : haveDefault
     * author : jp
     * 查询是否补充资料
     * @param $applyId
     * @return mixed
     */
    public function haveDefault($applyId)
    {
        return $this->model->where('apply_id', $applyId)->where('file_type', MATERIALS_TYPE['default'])->count();
    }



}