<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/6/4
 * Time: 16:19
 */

namespace App\Repositories\Policy;


use App\Common\Code;
use App\Criteria\KeywordCriteria;
use App\Criteria\OrderByCriteria;
use App\Criteria\Project\WhereCreatedEndCriteria;
use App\Criteria\Project\WhereCreatedStartCriteria;
use App\Criteria\Project\WhereEdateEltCriteria;
use App\Criteria\Project\WhereEdateGtCriteria;
use App\Criteria\Project\WhereSdateEltCriteria;
use App\Criteria\Project\WhereSdateGtCriteria;
use App\Criteria\StewardPush\StewardPushProjectCriteria;
use App\Criteria\StewardPush\UserPushProjectCriteria;
use App\Criteria\WhereEqualCriteria;
use App\Criteria\WhereInCriteria;
use App\Criteria\WhereLikeCriteria;
use App\Events\BatchDelete;
use App\Exceptions\QueryException;
use App\Http\Controllers\Service\DistrictService;
use App\Models\ProjectModel;
use App\Models\Steward\StewardPushRecordModel;
use App\Repositories\BaseRepository;
use App\Repositories\CommonRepository;

class ProjectRepository extends BaseRepository
{
    use CommonRepository;

    public function model()
    {
        return ProjectModel::class;
    }

    public function list($search_arr)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{

            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['province_code','city_code','district_code','publish_status']));

            $this->pushCriteria(new WhereCreatedStartCriteria($search_arr, 'start_time'));
            $this->pushCriteria(new WhereCreatedEndCriteria($search_arr, 'end_time'));
            $this->pushCriteria(new WhereEdateEltCriteria($search_arr, 'edate_elt'));
            $this->pushCriteria(new WhereEdateGtCriteria($search_arr, 'edate_gt'));
            $this->pushCriteria(new WhereSdateGtCriteria($search_arr, 'sdate_gt'));
            $this->pushCriteria(new WhereSdateEltCriteria($search_arr, 'sdate_elt'));

            $this->pushCriteria(new KeywordCriteria($search_arr, ['name']));

            $this->pushCriteria(new OrderByCriteria($search_arr));

            $this->with(['policy']);
            $res = $this->paginate($per_page);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);

    }

    public function detail($id)
    {
        $data = $this->model
            ->with(['mold','policy', 'materials','materialsOther', 'file', 'plate', 'staff', 'industry'])
            ->find($id);
        return empty($data) ? [] : $data->toArray();
    }

    public function getByLikeCode($data, $column = ['*'])
    {
        $data = $this->model->select($column)->where('code', 'like', $data.'%')->orderBy('id', 'desc')->first();
        return empty($data) ? [] : $data->toArray();
    }

    public function applyDetail($id, $column=['*'])
    {
        $data = $this->model->select($column)->with(['mold', 'materials' => function($query) {
            $query->select(['id', 'project_id', 'name', 'is_need', 'type']);
        }])->find($id);

        return empty($data) ? [] : $data->toArray();
    }

    public function getIndexNew($search_arr, $column = ['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['publish_status']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page, $column);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function getByEncId($enc_id, $column = ['*'])
    {
        $res = $this->model
            ->select($column)
            ->where('enc_id', $enc_id)
            ->where('publish_status', PUBLISH_STATUS['yes'])
            ->with(['mold','plate', 'materials','materialsOther', 'file', 'plate'])
            ->with(['policy' => function ($query) {
                $query->where('publish_status', PUBLISH_STATUS['yes']);
            }]);
        $user_id = (int)getLoginHome('id');
        if (!empty($user_id)) {
            $res = $res->withCount(['collections' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id)->where('obj_type', OBJ_TYPE['project']);
            }]);
        }
        $data = $res->first();

        return empty($data) ? [] : $data->toArray();
    }

    public function search($search_arr, $column)
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{

            $this->pushCriteria(new WhereEqualCriteria($search_arr, ['publish_status', 'province_code', 'city_code', 'district_code', 'mold_id']));
            $this->pushCriteria(new WhereEdateEltCriteria($search_arr, 'edate_elt'));
            $this->pushCriteria(new WhereEdateGtCriteria($search_arr, 'edate_gt'));
            $this->pushCriteria(new WhereSdateGtCriteria($search_arr, 'sdate_gt'));
            $this->pushCriteria(new WhereSdateEltCriteria($search_arr, 'sdate_elt'));

            $this->pushCriteria(new KeywordCriteria($search_arr, ['name']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
//            $this->pushCriteria(new StewardPushProjectCriteria($search_arr));
            $this->pushCriteria(new UserPushProjectCriteria($search_arr));

            $res = $this->paginate($per_page, $column);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);

    }

    public function deleteBatch($ids)
    {
//        event(new BatchDelete($ids, ACTIVITY_SUBJECT_TYPE['project']));
        return $this->model->destroy($ids);
    }

    public function allForOverview($where,$column= ['*'])
    {
        return $this->model->where($where)->withTrashed()->select($column)->get()->toArray();
    }

    public function getIdsTrashLike($name,$column= ['*'])
    {
        return $this->model->where('name', 'like', "%$name%")->withTrashed()->select($column)->get()->toArray();
    }

    public function allForOverviewByIds($ids,$column= ['*'])
    {
        return $this->model->whereIn('id', $ids)->withTrashed()->select($column)->get()->toArray();
    }

    public function conditionList($search_arr, $column=['*'])
    {
        $current_page = array_get($search_arr,'page',1);
        $per_page = isset($search_arr['per_page']) ? get_per_page($search_arr['per_page']):env('FRONT_PAGE_SIZE');
        try{
            $this->pushCriteria(new KeywordCriteria($search_arr, ['name']));
            $this->pushCriteria(new OrderByCriteria($search_arr));
            $res = $this->paginate($per_page, $column);
        }catch (\Illuminate\Database\QueryException $e){
            throw new QueryException(Code::DB_ERROR,$e->getMessage());
        }
        return page($res,$current_page);
    }

    public function collectionByIds($ids)
    {
        $column = [
            'id',
            'enc_id',
            'name',
            'sup_content',
            'mold_id',
            'validity_sdate',
            'validity_edate',
            'province_code',
            'city_code',
            'district_code',
            'created_at'
        ];

        if (empty($ids)) {
            return [];
        }
        $data = $this->model->select($column)->whereIn('id', $ids)->get()->toArray();

        if (empty($data)) {
            return [];
        }

        // 地区
        $code_arr = app(DistrictService::class)->getDistrictCode($data);

        foreach ($data as $key => &$value) {
            $value['province_name'] = array_get($code_arr, $value['province_code'], '');
            $value['city_name'] = array_get($code_arr, $value['city_code'],'');
            $value['district_name'] = array_get($code_arr, $value['district_code'],'');
        }
        return $data;
    }

}