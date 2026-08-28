<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/17
 * Time: 15:25
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\Log\ListRequest;
use App\Repositories\ActivityLogRepository;

class LogController extends Controller
{

    protected $activityLogRepository;

    public function __construct(ActivityLogRepository $activityLogRepository)
    {
        $this->activityLogRepository = $activityLogRepository;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);
        if (!empty($params['sort']) && $params['sort'] == SORT['asc']) {
            $params['order_by'] = ['id' => 'ASC'];
        } else {
            $params['order_by'] = ['id' => 'DESC'];
        }
        unset($params['sort']);
        $params['causer_id'] = 0;
        $data = $this->activityLogRepository->list($params);
        return codeRender(Code::OK, $data);
    }
}
