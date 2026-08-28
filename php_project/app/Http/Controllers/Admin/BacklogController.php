<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/28
 * Time: 14:40
 */

namespace App\Http\Controllers\Admin;


use App\Common\Code;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserMessage\BacklogListRequest;
use App\Http\Requests\UserMessage\ReadRequest;
use App\Repositories\Apply\ApprovalAcceptRepository;
use App\Repositories\User\UserMessageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BacklogController extends Controller
{

    protected $userMessageRepository;

    public function __construct(UserMessageRepository $userMessageRepository)
    {
        $this->userMessageRepository = $userMessageRepository;
    }

    public function list(BacklogListRequest $request)
    {
        $params = $this->filter($request);

        $param = [
            'user_id' => (int)getLoginStaff('id'),
            'user_type' => MESSAGE_USER_TYPE['staff'],
            'order_by' => [
                'id' => 'DESC'
            ]
        ];

        if (!empty($request->input('is_read')) && $request->input('is_read') == USER_MESSAGE_READ['not']) {
            $params['order_by']['is_read'] = 'ASC';
        }
        $params['order_by']['id'] = 'DESC';


        $param = array_merge($param, $params);

        $data = $this->userMessageRepository->list($param);

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

    /**
     * FUNCTION_NAME : read
     * author : jp
     * 读消息
     * @param ReadRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws QueryException
     */
    public function read(ReadRequest $request)
    {
        $param = [
            'user_id' => (int)getLoginStaff('id'),
            'user_type' => MESSAGE_USER_TYPE['staff'],
            'id' => $request->input('id')
        ];

        try {

            DB::beginTransaction();
            $message = $this->userMessageRepository->findRepository($request->input('id'));
            $this->userMessageRepository->read($param);
            if ($message['source_type_id'] == USER_MESSAGE_SOURCE['three']) {
                app(ApprovalAcceptRepository::class)->renew(['user_message_id' => $request->input('id')]);
            }
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (QueryException $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::FAIL, $e->getMessage());
        }
        return codeRender(Code::OK);
    }
}