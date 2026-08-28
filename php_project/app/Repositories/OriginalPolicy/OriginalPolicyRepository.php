<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/11
 * Time: 15:03
 */

namespace App\Repositories\OriginalPolicy;


use App\Models\OriginalPolicy\OriginalPolicyConclusionModel;
use App\Models\OriginalPolicy\OriginalPolicyDetailModel;
use App\Models\OriginalPolicy\OriginalPolicyGovModel;
use App\Models\OriginalPolicy\OriginalPolicyItemModel;
use App\Models\OriginalPolicy\OriginalPolicyModel;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class OriginalPolicyRepository extends BaseRepository
{

    public function model()
    {
        return OriginalPolicyModel::class;
    }

    public function getOne($id, &$res)
    {
        $type = [OBJ_TYPE['macro_policy'], OBJ_TYPE['sup_policy'], OBJ_TYPE['imple_regu'],
            OBJ_TYPE['announce'], OBJ_TYPE['publicity']];
        $res = $this->model
            ->whereIn('obj_type', $type)
            ->where('status', '!=', '-1')
            ->where('policy_id', '>',$id)
            //->with(['govAgen', 'item', 'conclusion', 'detail'])
            ->first();
        $this->resetModel();
        if (!empty($res)) {
            $res = $res->toArray();
             //这里直接写原生sql
            $gov_agen = OriginalPolicyGovModel::where('obj_id', $res['policy_id'])->get()->toArray();
            $res['gov_agen'] = $gov_agen;
            $item = OriginalPolicyItemModel::where('policy_id', $res['policy_id'])->get()->toArray();
            $res['item'] = $item;
            $conclusion = OriginalPolicyConclusionModel::where('policy_id', $res['policy_id'])->first();
            if (empty($conclusion)) {
                $conclusion = [];
            } else {
                $conclusion = $conclusion->toArray();
            }
            $res['conclusion'] = $conclusion;

            $detail = OriginalPolicyDetailModel::where('policy_id', $res['policy_id'])->first();
            if (empty($detail)) {
                $detail = [];
            } else {
                $detail = $detail->toArray();
            }
            $res['detail'] = $detail;
        } else {
            $res = [];
        }

//        return $res;
    }
}