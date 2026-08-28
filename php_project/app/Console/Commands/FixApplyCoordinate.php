<?php

namespace App\Console\Commands;

use App\Models\ApprovalCoordinateLogModel;
use App\Models\ApprovalCoordinateRelationModel;
use App\Models\ApprovalModel;
use App\Repositories\Staff\StaffDepartmentRepository;
use App\Repositories\Staff\StaffRepository;
use Illuminate\Console\Command;

class FixApplyCoordinate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:applyCoordinate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修复 协同部门日志';

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
            'type' => APPROVAL_TYPE['three'],
        ];
        $arr = ApprovalModel::where($where)->get()->toArray();

        if (empty($arr)) {
            return;
        }

        $applyIds = array_column($arr, 'apply_id');

        $where = [
            'type' => APPROVAL_TYPE['two'],
        ];
        $main = ApprovalModel::whereIn('apply_id', $applyIds)->where($where)->get()->toArray();
        if (empty($main)) {
            return;
        }
        $mainDepartmentId = array_unique(array_column($main, 'department_id'));
        
        $staff = [];
        foreach ($mainDepartmentId as $k => $v) {
            $tmp = app(StaffDepartmentRepository::class)->getOperatorStaff($v);
            if (!empty($tmp)) {
                $staff[$tmp['department_id']] = $tmp['staff_id'];
            }
        }

        $newStaff = [];
        foreach ($main as $km  => $vm) {
            $newStaff[$vm['apply_id']] = [
                'apply_id' => $vm['apply_id'],
                'approval_id' => $vm['id'],
                'staff_id' => array_get($staff, $vm['department_id'], 0),
            ];
        }

        $data = [];

        foreach ($arr as $key => $value) {
            $tmpApplyId =  $value['apply_id'];
           $data[$value['apply_id'].'-'.$value['created_at']]['apply_id'] = $tmpApplyId;
           $data[$value['apply_id'].'-'.$value['created_at']]['approval_id'] = array_get($newStaff[$tmpApplyId], 'approval_id', 0);
           $data[$value['apply_id'].'-'.$value['created_at']]['created_staff_id'] = array_get($newStaff[$tmpApplyId], 'staff_id', 0);
           $data[$value['apply_id'].'-'.$value['created_at']]['created_at'] = $value['created_at'];
           $data[$value['apply_id'].'-'.$value['created_at']]['updated_at'] = $value['created_at'];
           $data[$value['apply_id'].'-'.$value['created_at']]['coordinates'][] = $value['id'];
        }

        if (empty($data)) {
            return;
        }
        $data = array_values($data);

        $existCoor = ApprovalCoordinateLogModel::select(['approval_id' , 'created_at'])->get()->toArray();
        $exist = [];
        foreach ($existCoor as $ke => $ve) {
            $exist[] = $ve['approval_id'].'-'.$ve['created_at'];
        }

        $keys = ['apply_id', 'approval_id', 'created_staff_id', 'created_at', 'updated_at'];
        foreach ($data as $kd => $vd) {
            $tmpK = $vd['approval_id'].'-'.$vd['created_at'];
            if (in_array($tmpK,$exist)) {
                continue;
            }
            $save = array_only($vd, $keys);
            $res = ApprovalCoordinateLogModel::create($save);

            $coor = [];
            foreach ($vd['coordinates'] as $kk => $vv) {
                $coor[] = [
                    'approval_id' => $vv,
                    'log_id' => $res['id'],
                    'created_at' => $vd['created_at'],
                    'updated_at' => $vd['created_at'],
                ];
            }

            if (!empty($coor)) {
                ApprovalCoordinateRelationModel::insert($coor);
            }
        }


    }
}
