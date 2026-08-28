<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/11/5
 * Time: 18:46
 */

namespace App\Http\Controllers\Home;


use App\Common\Code;
use App\Events\Recommend;
use App\Exceptions\QueryException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\IndustryService;
use App\Http\Requests\UserFollowIndustry\DeleteRequest;
use App\Http\Requests\UserFollowIndustry\SaveRequest;
use App\Models\UserFollowIndustryModel;
use App\Repositories\User\UserFollowIndustryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserFollowIndustryController extends Controller
{

    protected  $repository;
    public function __construct(UserFollowIndustryRepository $repository)
    {
        $this->repository = $repository;
    }


    public function index(Request $request)
    {
        $user_id = (int)getLoginHome('id');

        $res  = $this->repository->getAll($user_id);
        if (!empty($res)) {
            $res= app(IndustryService::class)->getIndustryNameList($res);
        }

        $main = [];
        $vice = [];
        $follow = [];

        foreach ($res as $key => $value) {
            if ($value['type'] == USER_FOLLOW_INDUSTRY_TYPE['main']) {
                $main = $value;
            } elseif ($value['type'] == USER_FOLLOW_INDUSTRY_TYPE['vice']) {
                $vice = $value;
            }
            elseif ($value['type'] == USER_FOLLOW_INDUSTRY_TYPE['follow']) {
                $follow[] = $value;
            }
        }
        return codeRender(Code::OK, ['main' => $main, 'vice' => $vice, 'follow' => $follow]);
    }

    public function save(SaveRequest $request)
    {
        $whereMain = [
            'user_id' => (int)getLoginHome('id'),
            'type' => USER_FOLLOW_INDUSTRY_TYPE['main']
        ];
        $whereVice = [
            'user_id' => (int)getLoginHome('id'),
            'type' => USER_FOLLOW_INDUSTRY_TYPE['vice']
        ];

        $whereFollow = [
            'user_id' => (int)getLoginHome('id'),
            'type' => USER_FOLLOW_INDUSTRY_TYPE['follow']
        ];

        $column = [
            'first_industry_id',
            'second_industry_id',
            'third_industry_id',
            'fourth_industry_id',
        ];

        // 主行业
        $main = $request->input('main');
        $mainData = $this->initValue(array_only($main, $column));

        // 副行业
        $vice = $request->input('vice', []);
        if (!empty($vice)) {
            $viceData = $this->initValue(array_only($vice, $column));
        }

        // 关注行业
        $follow = $request->input('follow', []);

        $store_data = [];
        $exist = [];

        $timeArr = returnCreatedUpdatedAt();
        $updateColumn = $column;
        array_unshift($updateColumn, 'id');
        $unique = [];
        $update = [];
        foreach ($follow as $key => $value) {
            // 判重
            $value = $this->initValue($value);
            $tmp = implode('-',array_values(array_only($value, $column)));
            if (in_array($tmp, $unique)) {
                continue;
            }
            $unique[] = $tmp;
            if (empty($value['id'])) {
                $store_data[] = array_merge(array_only($value, $column), $whereFollow, $timeArr);
            } elseif (!empty($value['id'])) {
                $exist[] = $value['id'];
                $update[] = array_only($value, $updateColumn);
            }
        }
        
        try {
            DB::beginTransaction();
            $res1 = $this->repository->selfUpdateOrCreate($whereMain, $mainData);

            $res2 = 0;
            if (empty($vice)) {
                $this->repository->deleteVice($whereVice);
            } else {
                $res2 = $this->repository->selfUpdateOrCreate($whereVice, $viceData);
            }
            $this->repository->deleteFollow($whereFollow, $exist);

            if (!empty($store_data)) {
                $this->repository->storeBatchRepository($store_data);
            }
            if (!empty($update)) {
                $this->repository->updateBatchRepository(UserFollowIndustryModel::TABLE_NAME, $update, 'id', $whereFollow);
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new QueryException(Code::DB_ERROR, $e->getMessage());
        }

        // 推荐事件
        if (!empty($res1) || !empty($res2) || !empty($store_data) || empty($update)) {
            event(new Recommend(['obj_id' => (int)getLoginHome('id'), 'obj_type' => OBJ_TYPE['user']]));
        }

        return codeRender(Code::OK);
    }

    public function delete(DeleteRequest $request)
    {
        $this->repository->deleteRepository($request->input('id'));
        return codeRender(Code::OK);
    }

    public function initValue($data)
    {
        $keys = [
            "first_industry_id" => 0,
            "second_industry_id" => 0,
            "third_industry_id" => 0,
            "fourth_industry_id" => 0,
        ];
        foreach ($keys as $key => $value) {
            $data[$key] = empty($data[$key]) ? $value : $data[$key];
        }
        return $data;
    }


}