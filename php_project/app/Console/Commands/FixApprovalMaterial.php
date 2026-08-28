<?php

namespace App\Console\Commands;

use App\Models\ApplyFileModel;
use App\Models\ApprovalMaterialModel;
use App\Models\ApprovalModel;
use App\Repositories\Apply\ApprovalMaterialRepository;
use App\Repositories\Staff\StaffDepartmentRepository;
use Illuminate\Console\Command;

class FixApprovalMaterial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:approvalMaterial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修复补充资料的记录';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this->deal();
    }

    public function deal()
    {
        $where = [
            'created_staff_id' => 0,
        ];

        $data = ApprovalMaterialModel::where($where)->get()->toArray();
        if (empty($data)) {
            return;
        }

        $approvalId = array_column($data, 'approval_id');

        $approval = ApprovalModel::whereIn('id', $approvalId)->get()->toArray();

        $apply_id = array_column($approval, 'apply_id');
        $relation = array_column($approval, 'apply_id', 'id');
        $relationDepartment = array_column($approval, 'department_id', 'id');

        $column = ['apply_id', 'file_name', 'file_url', 'file_type','created_at'];
        $file = ApplyFileModel::select($column)->whereIn('apply_id', $apply_id)->where('file_type', MATERIALS_TYPE['default'])->get()->toArray();
        $newFile = [];
        foreach ($file as $kf => $vf) {
            $newFile[$vf['apply_id']][] = $vf;
        }

        $department_id = array_unique(array_column($approval, 'department_id'));
        $staff = app(StaffDepartmentRepository::class)->getOperatorStaffByIds($department_id);
        $staff = array_column($staff, 'staff_id', 'department_id');


        foreach ($data as $k => $v) {
            $tmpApply = array_get($relation, $v['approval_id'], 0);
            if (empty($tmpApply)) {
                continue;
            }
            $tmp = [];
            if ($v['status'] == MATERIAL_SEND_STATUS['three']) {
                $tmpFile = $newFile[$tmpApply]??[];
                $tmp['submit_time'] = array_get($tmpFile[0]??[],'created_at',0);
                $tmp['material'] = json_encode($tmpFile, JSON_UNESCAPED_UNICODE);
            }

            $tmpDepartment = array_get($relationDepartment, $v['approval_id'], 0);
            $staff_id = array_get($staff, $tmpDepartment, 0);
            if ($staff_id) {
                $tmp[CREATED_STAFF_ID] = $staff_id;
            }

            if (!empty($tmp)) {
                $tmp['id'] =$v['id'];
                app(ApprovalMaterialRepository::class)->updateRepository($tmp);
            }
        }
    }
}
