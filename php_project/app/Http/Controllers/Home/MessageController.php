<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/10
 * Time: 12:27
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserMessage\DetailRequest;
use App\Http\Requests\UserMessage\HomeListRequest;
use App\Repositories\User\UserMessageRepository;
use Illuminate\Http\Request;

class MessageController extends Controller
{

    protected $repository;

    public function __construct(UserMessageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list(HomeListRequest $request)
    {
        $search = [
            'user_id' => getLoginHome('id'),
            'user_type' => MESSAGE_USER_TYPE['user'],
            'order_by' => [
                'id' => 'DESC'
            ]
        ];

        if (!blank($request->input('source_type_id'))) {
            $search['source_type_id'] = $request->input('source_type_id');
        }

        $data = $this->repository->list($search);

        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        foreach ($data['data'] as $key => $value) {
            if (empty($value['title'])) {
                $data['data'][$key]['title'] = array_get(trans('constant.user_message_source'), $value['source_type_id'], '');
            }
        }

        return codeRender(Code::OK, $data);
    }

    public function detail(DetailRequest $request)
    {

        $where = [
            'id' => $request->input('id'),
            'user_id' => (int)getLoginHome('id'),
            'user_type' => MESSAGE_USER_TYPE['user'],
        ];

        $data = $this->repository->detail($where);

        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }

        if ($data['is_read'] == USER_MESSAGE_READ['not']) {
            $this->repository->updateRead($request->input('id'));
            $data['is_read'] = USER_MESSAGE_READ['is'];
        }

        return codeRender(Code::OK, $data);
    }

    /**
     * FUNCTION_NAME : unReadNum
     * author : jp
     * 消息未读数
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function unReadNum()
    {
        $where = [
            'user_id' => getLoginHome('id'),
            'user_type' => MESSAGE_USER_TYPE['user']
        ];
        $data = $this->repository->unReadNum($where);

        return codeRender(Code::OK, $data);
    }
}