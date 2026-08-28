<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/23
 * Time: 17:10
 */

namespace App\Repositories\Apply;


use App\Events\ApplyPdfCreate;
use App\Models\ApplyModel;
use App\Models\ApplyPdfModel;
use App\Repositories\BaseRepository;
use App\Repositories\PdfRepository;
use Illuminate\Support\Facades\Log;

class ApplyPdfRepository extends BaseRepository
{

    public function model()
    {
        return ApplyPdfModel::class;
    }

    // 获取pdf 不存在就生成
    public function getPdf($arr)
    {
        if (empty($arr['id'])) {
            return '';
        }
        $res = $this->model->where('apply_id', $arr['id'])->orderBy('id', 'DESC')->first();
        if (empty($res)) {
            $this->pdfCreate($arr);
            return '';
        } elseif (!empty($res['url'])) {
            return $res['url'];
        } else {
            $res = $res->toArray();
            $result = app(PdfRepository::class)->getPdf($res);
            if (empty($result)) {
                return '';
            } elseif (empty($result['data'])) {
                return '';
            } else {
                $arr = [
                    'id' => $res['id'],
                    'url' => $result['data'],
                ];
                try {
                    $this->updateRepository($arr);
                } catch (\Exception $e) {
                    Log::error('update apply pdf error: '.$e->getMessage());
                    return '';
                }
                return $result['data'];
            }

        }
    }

    public function pdfCreate($arr)
    {
        $business_id = businessId();
        $data = [
            'business_id' => $business_id,
            'apply_id' => $arr['id'],
        ];
        try {
            $this->storeRepository($data);
            $arr['business_id'] = $business_id;
            event(new ApplyPdfCreate($arr));
        } catch (\Exception $e) {
            Log::error('pdfCreate error: '. $e->getMessage());
        }

    }
}

