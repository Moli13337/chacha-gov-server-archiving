<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/10/14
 * Time: 16:50
 */

namespace App\Http\Controllers\Service;


use App\Models\ApplyModel;
use Composer\Util\Zip;
use Illuminate\Support\Facades\Log;
use Xkd\Upload\Upload;

class ZipService extends BaseService
{


    public function create($params)
    {

    }


    public function get($business_id)
    {
        $params = [
            'business_id' => $business_id
        ];
        try {
            $data = Upload::select('zip')->detail($params);
            return $data;
        } catch (\Exception $e) {
            Log::error('Upload create zip fail.' . $e->getMessage());
            return false;
        }
    }

    public function createApplyZip($apply)
    {
        $where = [
            'id' => $apply['id'],
        ];

        $res = false;
        if (empty($apply['business_id'])) {
            $business_id = businessId();
            try {

                $update = [
                    'zip_business_id' => $business_id,
                    'zip_url' => '',
                ];
                $res = ApplyModel::where($where)->update($update);
            } catch (\Exception $e) {
                Log::error('createApprovalPdf error: '. $e->getMessage());
            }

            if (!$res) {
                return;
            }

            $apply['business_id'] = $business_id;
        }

        try {
            $res = Upload::select('zip')->create($apply);
        } catch (\Exception $e) {
            Log::error('createApprovalPdf error: '. $e->getMessage());
        }
    }
}