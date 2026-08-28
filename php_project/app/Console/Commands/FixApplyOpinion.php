<?php

namespace App\Console\Commands;

use App\Models\ApplyModel;
use App\Models\ApprovalMarkModel;
use App\Models\ApprovalModel;
use App\Models\ApprovalOpinionModel;
use Illuminate\Console\Command;

class FixApplyOpinion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:applyOpinion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '修复 企业服务中心受理的时候的信息';

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
            'type' => APPROVAL_TYPE['one'],
            'status' => APPROVAL_STATUS['two']
        ];
        $arr = ApprovalModel::where($where)->get()->toArray();

        if (empty($arr)) {
            return;
        }

        $where = [
            'type' => APPROVAL_MARK_TYPE['one'],
        ];
        $approval_ids = array_column($arr, 'id');
        $apply_ids = array_column($arr, 'apply_id');

        $mark = ApprovalMarkModel::whereIn('approval_id', $approval_ids)->where($where)->get()->toArray();
        $mark = array_column($mark, 'mark', 'approval_id');

        $apply = ApplyModel::whereIn('id', $apply_ids)->select(['id', 'apply_status'])->get()->toArray();
        $apply = array_column($apply, 'apply_status', 'id');

        $exist = ApprovalOpinionModel::whereIn('approval_id', $approval_ids)->get()->toArray();
        $exist = array_column($exist, 'approval_id');

        // 查主审部门创建的时间
        $where = [
            'type' => APPROVAL_TYPE['two'],
        ];
        $mainTime = ApprovalModel::where($where)->select(['apply_id', 'created_at'])->get()->toArray();
        $mainTime = array_column($mainTime, 'created_at', 'apply_id');

        $time = time();
        $data = [];
        foreach ($arr as $key => $value) {
            $tmpApplyId = $value['apply_id'];
            $tmpApprovalId = $value['id'];
            if (empty($apply[$tmpApplyId]) ) {
                continue;
            }

            if (in_array($tmpApprovalId,$exist)) {

                continue;
            }

            if ($apply[$tmpApplyId] == APPLY_STATUS['four']) {
                $data[] = [
                    'approval_id' => $tmpApprovalId,
                    'department_mark' => array_get($mark, $tmpApprovalId, ''),
                    'created_at' => $value['created_at']
                ];
            }   elseif ($apply[$tmpApplyId] > APPLY_STATUS['four']) {
                $data[] = [
                    'approval_id' => $tmpApprovalId,
                    'department_mark' => '已受理',
                    'created_at' => array_get($mainTime, $tmpApplyId, $time)
                ];
            }
        }

        if (empty($data)) {
            return;
        }
        ApprovalOpinionModel::insert($data);

    }
}
