<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/6
 * Time: 11:20
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Events\LogCommon;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserFeedback\ListRequest;
use App\Http\Requests\UserFeedback\ReplyUserFeedbackRequest;
use App\Repositories\User\UserFeedbackRepository;
use App\Repositories\User\UserMessageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserFeedbackController extends Controller
{

    protected $repository;

    public function __construct(UserFeedbackRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);

        $params['is_replay'] = FEEDBACK_REPLY['user'];
        $params['order_by'] = [
            'created_at' => 'DESC'
        ];
        $data = $this->repository->search($params);
        return codeRender(Code::OK, $data);
    }

    public function reply(ReplyUserFeedbackRequest $request)
    {
        // 注明 回复的时候 内容是content 备注是title
        $params = $this->filter($request);
        $params['user_id'] = getLoginStaff('id');
        $params['is_reply'] = FEEDBACK_REPLY['staff'];

        $update = [
            'id' => $params['source_id'],
            'status' => FEEDBACK_STATUS['is'],
        ];

        $feed = $this->repository->detail($params['source_id']);

        if (empty($feed)) {
            return codeRender(Code::FAIL);
        }

        $relation = [
            FEEDBACK_TYPE['suggest'] => ACTIVITY_SUBJECT_TYPE['feedback_suggest'],
            FEEDBACK_TYPE['complaint'] => ACTIVITY_SUBJECT_TYPE['feedback_complaint'],
            FEEDBACK_TYPE['consult'] => ACTIVITY_SUBJECT_TYPE['feedback_consult'],
        ];

        $type = array_get($relation, $feed['type'], 0);

        try {
            DB::beginTransaction();

            $data = $this->repository->storeRepository($params);
            $this->repository->updateRepository($update);
            event(new LogCommon([
                'type' => ACTIVITY_TYPE['created'],
                'description' => trans('mysqlColumn.feedback.replay'),
                'subject_id' => $feed['id'],
                'attribute' => $data,
                'old' => [],
            ],  $type));

            $message = [
                'content' => $params['content'],
                'user_id' => $feed['user_id'],
                'type' => USER_MESSAGE_TYPE['announce'],
                'source_type_id' => USER_MESSAGE_SOURCE['feedback'],
                'target_id' => $data['id'],
            ];

            app(UserMessageRepository::class)->storeRepository($message);
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        return codeRender(Code::OK);
    }

    /**
     *
     * @api GET /api/feedback/todo  待处理数
     * @apiVersion 1.0.0
     * @apiName FeedbackTodo
     * @apiGroup 运行端--用户反馈
     *
     * @apiHeader {String} Authorization 用户授权token
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Authorization": "xxx",
     *     }
     *
     * @apiParam {String} Parm1 参数1
     * @apiParam {Number} Parm2 参数2
     *
     * @apiSuccess {Number} code 返回代码
     * @apiSuccess {String} message 返回信息
     * @apiSuccess {Object} data 返回数据块
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "code": 200,
     *       "message": "操作成功",
     *         "data":{
     *            "suggest": 18,
     *            "complaint": 28,
     *            "consult": 19
     *        }
     *     }
     *
     *
     * @apiErrorExample {json} Error-Response:
     * {"code":5001,"message":"接口异常"}
     */
    public function todoNUm(Request $request)
    {
        $data = $this->repository->todoNum();
        return codeRender(Code::OK, $data);
    }
}