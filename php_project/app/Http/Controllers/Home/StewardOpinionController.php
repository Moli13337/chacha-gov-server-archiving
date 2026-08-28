<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/7
 * Time: 10:19
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StewardOpinion\HomeDetailRequest;
use App\Http\Requests\StewardOpinion\HomeListRequest;
use App\Http\Requests\StewardOpinion\HomeSubmitRequest;
use App\Repositories\Steward\StewardOpinionRepository;
use App\Repositories\Steward\StewardUserOpinionFileRepository;
use App\Repositories\Steward\StewardUserOpinionRepository;
use App\Repositories\Steward\StewardUserRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\DB;

class StewardOpinionController extends Controller
{

    protected $repository;
    public function __construct(StewardOpinionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list(HomeListRequest $request)
    {
        $params = $this->filter($request);
        $params['publish_status'] = PUBLISH_STATUS['yes'];

        $params['order_by'] = ['id' => 'DESC'];
        $column = [
            'id',
            'enc_id',
            'title',
            'content',
            'source_name',
            'type',
            'link',
            'publish_status',
            'publish_time',
            'created_at',
        ];
        $data = $this->repository->clientList($params, $column);
        if (empty($data['data'])) {
            return codeRender(Code::OK, $data);
        }

        foreach ($data['data'] as $key => &$value) {
            $value['id'] = $value['enc_id'];
        }

        return codeRender(Code::OK, $data);

    }

    public function detail(HomeDetailRequest $request)
    {
        $where = [
            'enc_id' => $request->input('id'),
            'publish_status' => PUBLISH_STATUS['yes']
        ];
        $column = [
            'id',
            'enc_id',
            'title',
            'content',
            'source_name',
            'type',
            'link',
            'publish_status',
            'publish_time',
            'created_at',
        ];
        $data = $this->repository->detail($where, $column);
        if (empty($data)) {
            return codeRender(Code::OK, $data);
        }
        $data['id'] = $data['enc_id'];
        return codeRender(Code::OK, $data);
    }

    public function submit(HomeSubmitRequest $request)
    {
        $user_id = (int)getLoginHome('id');
        $enterprise = app(UserRepository::class)->enterpriseDetail($user_id);

        $where = [
            'enc_id' => $request->input('id'),
        ];
        $opinion = $this->repository->detail($where, ['id']);

        $store = [
            'user_id' => $user_id,
            'user_name' => (string)getLoginHome('name'),
            'mobile' => (string)getLoginHome('mobile'),
            'enterprise_id' => array_get($enterprise, 'id', 0),
            'enterprise_name' => array_get($enterprise, 'name', ''),
            'steward_opinion_id' => array_get($opinion, 'id', ''),
            'content' => $request->input('content', ''),
        ];

        try {
            DB::beginTransaction();
            $res = app(StewardUserOpinionRepository::class)->storeRepository($store);
            $this->storeFile($request, $res['id']);
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

    public function storeFile($request, $id)
    {
        $file = $request->input('file', []);

        $column = ['name', 'save_url'];
        $file = array_map(function ($v) use ($column) {
            return array_only($v, $column);
        }, $file);
        if (!empty($file)) {
            foreach ($file as $key => $value) {
                $file[$key]['steward_user_opinion_id'] = $id;
                $file[$key] = array_merge($file[$key], returnCreatedUpdatedAt());
            }
            app(StewardUserOpinionFileRepository::class)->storeBatchRepository($file);
        }
    }
}