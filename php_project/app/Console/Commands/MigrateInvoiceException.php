<?php

namespace App\Console\Commands;

use App\Models\ApplyFileExceptionBakModel;
use App\Models\ApplyFileExceptionModel;
use App\Models\ApplyFileModel;
use App\Models\ApplyModel;
use App\Models\ApprovalModel;
use Illuminate\Console\Command;

class MigrateInvoiceException extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:invoiceException';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发票流程变更，迁移发票异常信息';

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
        $this->migrate();
    }

    public function migrate()
    {
        // 取出过了预处理这一步后的数据 申报

        $where = [
//            ['id', '=', 40],
            ['apply_status', '>=', APPLY_STATUS['two']]
        ];
        $apply = ApplyModel::select(['id'])->where($where)->get()->toArray();

        foreach ($apply as $key => $value) {
            $this->deal($value['id']);
        }
    }

    public function deal($id)
    {
        // 取出其中含有发票的数据
        $where = [
            ['apply_id', '=', $id],
            ['file_type', '=', MATERIALS_TYPE['invoice']],
            ['check_status', '>', APPLY_CHECK_STATUS['init']]
        ];
        $arr = ApplyFileModel::where($where)->get()->toArray();
        if (empty($arr)) {
            return;
        }
        // 取出到企业服务的审批时间作为迁移数据的预处理数据的创建时间
        $where = [
            ['apply_id', '=', $id],
            ['type', '=', APPROVAL_TYPE['one']],
        ];
        $approval = ApprovalModel::where($where)->first()->toArray();

        $created_time = $approval['created_at'];

        foreach ($arr as $key => $value) {
            if ($value['check_status'] == APPLY_CHECK_STATUS['normal']) {
                $this->normal($value, $created_time);
            } elseif ($value['check_status'] == APPLY_CHECK_STATUS['error']) {
                $this->dealError($value, $created_time);
            }
        }
    }

    public function normal($arr, $created_time)
    {
        $data = [
            'apply_id' => $arr['apply_id'],
            'apply_file_id' => $arr['id'],
            'created_at' => $created_time,
            'updated_at' => $created_time,
        ];

//        ApplyFileExceptionModel::create($data);
    }

    public function dealError($arr, $created_time)
    {
//         取出 exception中的数据
        $where = [
            'apply_file_id' => $arr['id'],
            'apply_id' => $arr['apply_id'],
        ];

        $exception = ApplyFileExceptionBakModel::where($where)->get()->toArray();
        if (!$exception) {
            return;
        }

        $remark = '';

        $data = [
            'status' => APPLY_EXCEPTION_STATUS['fail']
        ];
        $data = array_merge($data, $where);

        foreach ($exception as $key => $value) {
            if (!empty($value['remark'])) {
                $remark .= $value['remark'];
            }
            if ($value['type'] == APPLY_EXCEPTION_TYPE['one']) {
                $data['ocr'] = APPLY_EXCEPTION_OCR['fail'];
                $remark .= $value['remark'];
            } elseif ($value['type'] == APPLY_EXCEPTION_TYPE['two']) {
                $data['is_truth'] = APPLY_EXCEPTION_TRUTH['not'];
            } elseif ($value['type'] == APPLY_EXCEPTION_TYPE['three']) {
                $data['repeat'] = APPLY_EXCEPTION_REPEAT['yes'];
            } elseif ($value['type'] == APPLY_EXCEPTION_TYPE['four']) {
                $data['repeat_apply'] = APPLY_EXCEPTION_REPEAT_APPLY['yes'];
            }
        }
        $data['remark'] = $remark;
        $data['created_at'] = $data['updated_at'] = $exception[0]['created_at'];

        if (!isset($data['ocr'])) {
            $data['ocr'] = APPLY_EXCEPTION_OCR['success'];
        }

        if ($data['ocr'] == APPLY_EXCEPTION_OCR['success']) {
            if (!isset($data['repeat_apply'])) {
                $data['repeat_apply'] = APPLY_EXCEPTION_REPEAT_APPLY['no'];
            }

            if (!isset($data['repeat'])) {
                $data['repeat'] = APPLY_EXCEPTION_REPEAT_APPLY['no'];
            }

            if (!isset($data['is_truth'])) {
                $data['is_truth'] = APPLY_EXCEPTION_TRUTH['yes'];
            }

            $data['is_year'] = APPLY_EXCEPTION_YEAR['yes'];
        }
        ApplyFileExceptionModel::create($data);
    }
}
