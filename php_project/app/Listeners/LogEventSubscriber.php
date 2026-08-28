<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/7/24
 * Time: 14:30
 */

namespace App\Listeners;


use App\Repositories\ActivityLogRepository;
use App\Repositories\Policy\PolicyRepository;
use App\Repositories\Policy\PolicyUnscrambleRelationRepository;
use App\Repositories\Policy\ProjectFileRepository;
use App\Repositories\Policy\ProjectPlateRepository;

class LogEventSubscriber
{
    /**
     * FUNCTION_NAME : policyDeleteBatch
     * author : jp
     * 政策批量删除
     * @param $event
     */
    public function policyDeleteBatch($event)
    {
        $exist = app(PolicyRepository::class)->detailById($event->params[0]??0, 'obj_type');
        if (empty($exist)) {
            return;
        }
        $data['type'] = ACTIVITY_TYPE['deleted'];
        $data['subject_type_id'] = $exist['obj_type'];
        $data['properties'] = json_encode(['attribute' => $event->params, 'old' => []]);
        $data['title'] = array_get(trans('constant.activity_type'), ACTIVITY_TYPE['deleted'], '');
        $data['description'] = array_get(trans('constant.activity_subject_type'), $exist['obj_type'], '');
        $this->store($data);
    }

    /**
     * FUNCTION_NAME : deleteBatch
     * author : jp
     * 批量删除
     * @param $event
     */
    public function deleteBatch($event)
    {
        if (empty($event->subject_type_id)) {
            return;
        }
        $data['type'] = ACTIVITY_TYPE['deleted'];
        $data['subject_type_id'] = $event->subject_type_id;
        $data['properties'] = json_encode(['attribute' => $event->params, 'old' => []]);
        $data['description'] = array_get(trans('constant.activity_subject_type'), $data['subject_type_id'], '');
        $this->store($data);
    }

    public function logCommon($event)
    {
        $only = [
            'type',
            'description',
            'subject_id',
        ];
        $data = array_only($event->params, $only);
        $data['subject_type_id'] = $event->subject_type_id;
        $data['properties'] = json_encode([
            'attribute' => array_get($event->params, 'attribute', []),
            'old' => array_get($event->params, 'old', [])
        ]);
        $this->store($data);
    }

    public function store($data)
    {
        $data['title'] = array_get(trans('constant.activity_type'), $data['type'], '');
        $data['causer_id'] = (int)getLoginStaff('id');
        $data['causer_name'] = empty(getLoginStaff('name')) ? '' : getLoginStaff('name');
        app(ActivityLogRepository::class)->store($data);
    }

    public function govAgenChange($event)
    {
        $data = array_only($event->params, ['type', 'subject_id', 'subject_type_id','properties']);
        if (empty($data['subject_type_id'])) {
            return;
        }
        $data['type'] = ACTIVITY_TYPE['updated'];
        $data['description'] = trans('validation.attributes.gov_agen');
        $this->store($data);
    }

    public function fileChange($event)
    {
        $data = array_only($event->params, ['type', 'subject_id', 'subject_type_id','properties']);
        if (empty($data['subject_type_id'])) {
            return;
        }
        if (empty($data['type'])) {
            $data['type'] = ACTIVITY_TYPE['updated'];
        }

        if (empty($data['description'])) {
            $data['description'] = trans('validation.attributes.file');
        }

        $this->store($data);
    }

    public function policyUnscrambleChange($event)
    {
        $data = array_only($event->params, ['type', 'subject_type_id','properties']);
        if (empty($data['subject_type_id']) || empty($event->params['id'])) {
            return;
        }
        $data['subject_id'] = $event->params['id'];
        $exist_policy = app(PolicyUnscrambleRelationRepository::class)->policyById($data['subject_id']);
        $exist_policy = array_column($exist_policy, 'policy_id');
        $change = [];

        $new = empty(array_get($event->params, 'data')) ? [] :array_get($event->params, 'data');
        foreach ($new as $key => $v) {
            $change[] = $v['policy_id'];
        }

        if (count($change) != count($exist_policy) || !empty(array_diff($change, $exist_policy))) {
            $data['properties'] = json_encode(['attributes' => $change, 'old' => $exist_policy]);
            $data['description'] = trans('validation.attributes.relation_policy');
            $this->store($data);
        }
    }

    public function projectFileChange($event)
    {
        $data = array_only($event->params, ['type', 'subject_type_id','properties']);
        if (empty($data['subject_type_id']) || empty($event->params['id'])) {
            return;
        }
        $data['subject_id'] = $event->params['id'];
        $have = app(ProjectFileRepository::class)->getByProject(['project_id' => $data['subject_id']], ['save_url']);
        $haveArr = array_column($have, 'save_url');
        $flag = false;

        $new = empty(array_get($event->params, 'data')) ? [] :array_get($event->params, 'data');
        foreach ($new as $k => $v) {
            if (!in_array($v['save_url'], $haveArr)) {
                $flag = true;
                break;
            }
        }


        if (count($have) != count($new) || $flag) {
            $data['properties'] = json_encode(['attributes' => $new, 'old' => $have]);
            $data['description'] =  trans('validation.attributes.file');
            $this->store($data);
        }
    }

    public function materialsChange($event)
    {
        $data = array_only($event->params, ['type', 'subject_id', 'subject_type_id','properties']);
        if (empty($data['subject_type_id'])) {
            return;
        }
        $data['type'] = ACTIVITY_TYPE['updated'];
        $data['description'] = trans('validation.attributes.materials');
        $this->store($data);
    }

    public function materialsOtherChange($event)
    {
        $data = array_only($event->params, ['type', 'subject_id','subject_type_id','properties']);
        if (empty($data['subject_type_id'])) {
            return;
        }
        $data['type'] = ACTIVITY_TYPE['updated'];
        $data['description'] = trans('validation.attributes.materials_other');
        $this->store($data);
    }

    public function projectPlateChange($event)
    {
        $data = array_only($event->params, ['type', 'subject_type_id','properties']);
        if (empty($data['subject_type_id']) || empty($event->params['id'])) {
            return;
        }
        $data['subject_id'] = $event->params['id'];
        $plate_key = ['title', 'content'];
        $have = app(ProjectPlateRepository::class)->getByProject($data['subject_id'], $plate_key);
        $haveArr = array_map(function ($item) use ($plate_key) {
            return implode('-', array_only($item, $plate_key));
        }, $have);

        $haveArr = array_column($have, 'save_url');
        $flag = false;

        $new = empty(array_get($event->params, 'data')) ? [] :array_get($event->params, 'data');
        foreach ($new as $k => $v) {
            if (!in_array(implode('-', array_only($v, $plate_key)), $haveArr)) {
                $flag = true;
                break;
            }
        }

        if (count($have) != count($new) || $flag) {
            $data['properties'] = json_encode(['attributes' => $new, 'old' => $have]);
            $data['description'] =  trans('validation.attributes.plate');
            $this->store($data);
        }
    }

    public function subscribe($events)
    {
        $events->listen(
            'App\Events\PolicyBatchDelete',
            'App\Listeners\LogEventSubscriber@policyDeleteBatch'
        );

        $events->listen(
            'App\Events\BatchDelete',
            'App\Listeners\LogEventSubscriber@deleteBatch'
        );

        $events->listen(
            'App\Events\LogCommon',
            'App\Listeners\LogEventSubscriber@logCommon'
        );

        $events->listen(
            'App\Events\GovAgenChange',
            'App\Listeners\LogEventSubscriber@govAgenChange'
        );

        $events->listen(
            'App\Events\FileChange',
            'App\Listeners\LogEventSubscriber@fileChange'
        );

        $events->listen(
            'App\Events\PolicyUnscrambleChange',
            'App\Listeners\LogEventSubscriber@policyUnscrambleChange'
        );

        $events->listen(
            'App\Events\ProjectFileChange',
            'App\Listeners\LogEventSubscriber@projectFileChange'
        );

        $events->listen(
            'App\Events\MaterialsChange',
            'App\Listeners\LogEventSubscriber@materialsChange'
        );

        $events->listen(
            'App\Events\MaterialsOtherChange',
            'App\Listeners\LogEventSubscriber@materialsOtherChange'
        );

        $events->listen(
            'App\Events\ProjectPlateChange',
            'App\Listeners\LogEventSubscriber@projectPlateChange'
        );
    }
}