<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/6
 * Time: 17:20
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\StewardPush\TrumpetRequest;
use App\Repositories\Steward\StewardPushRecordRepository;
use App\Repositories\Steward\StewardPushRepository;
use Illuminate\Http\Request;

class StewardPushController extends Controller
{

    protected $repository;
    protected $stewardPushRecordRepository;
    public function __construct(StewardPushRepository $repository,
                                StewardPushRecordRepository $stewardPushRecordRepository)
    {
        $this->repository = $repository;
        $this->stewardPushRecordRepository = $stewardPushRecordRepository;
    }

    public function trumpet(TrumpetRequest $request)
    {
        $params = $this->filter($request);
        $params['user_id'] = (int)getLoginHome('id');
        $params['order_by'] = ['id' => 'DESC'];
        $column = ['id','steward_push_id','content', 'created_at'];
        $data = $this->stewardPushRecordRepository->trumpet($params, $column);
        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        $arr = trans('constant.steward_push_obj_type');
        foreach ($data['data'] as $key => &$value) {
            $value['id'] = array_get($value['source_push'], 'obj_enc_id');
            $value['obj_type'] = array_get($value['source_push'], 'obj_type');
            $tmp =  array_get($value['source_push'], 'obj_type');
            $value['obj_type_name'] =  !$tmp? '' : array_get($arr, $tmp, '');
            unset($value['source_push']);
        }
        return codeRender(Code::OK, $data);

    }
}