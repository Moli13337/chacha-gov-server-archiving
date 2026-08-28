<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/6
 * Time: 9:39
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserFeedback\ListRequest;
use App\Http\Requests\UserFeedback\SaveUserFeedbackRequest;
use App\Repositories\User\UserFeedbackRepository;
use App\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserFeedbackController extends Controller
{

    protected $repository;

    public function __construct(UserFeedbackRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * FUNCTION_NAME : store
     * author : jp
     * 反馈
     * @param SaveUserFeedbackRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws QueryException
     */
    public function store(SaveUserFeedbackRequest $request)
    {
        $white = [
            'title',
            'content',
            'type'
        ];
        $params = Collection::filter($white, $request->all());

        $params['user_id'] = getLoginHome('id');

        try {
            DB::beginTransaction();
            $data = $this->repository->storeRepository($params);
            $update = [
                'id' => $data['id'],
                'source_id' => $data['id'],
            ];
            $this->repository->updateRepository($update);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }
        return codeRender(Code::OK);
    }

    public function list(ListRequest $request)
    {
        $params = $this->filter($request);

        $params['is_reply'] = FEEDBACK_REPLY['user'];
        $params['order_by'] = [
            'id' => 'DESC'
        ];
        $params['user_id'] = (int)getLoginHome('id');

        $column = [
            'id',
            'title',
            'content',
            'type',
            'status',
            'is_reply',
            'source_id'
        ];
        $data = $this->repository->search($params,$column);
        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        foreach ($data['data'] as $key => &$value) {
            unset($value['user']);
            $value['reply'] = array_only($value['reply'], ['content', 'created_at']);
            if (empty($value['reply'])) {
                $value['reply']['content'] = '';
                $value['reply']['created_at'] = 0;
            }
        }
        return codeRender(Code::OK, $data);
    }
}