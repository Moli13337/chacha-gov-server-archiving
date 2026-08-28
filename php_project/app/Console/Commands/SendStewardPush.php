<?php

namespace App\Console\Commands;

use App\Models\Steward\StewardPushRecordModel;
use App\Repositories\SmsRepository;
use App\Repositories\Steward\StewardPushRecordRepository;
use Illuminate\Console\Command;

class SendStewardPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:steward:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send sms of steward push';

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
        $this->send();
    }

    public function send()
    {
        $where = [
            'is_send' => 0
        ];
        $data = StewardPushRecordModel::where($where)->whereHas('sourcePush', function ($query){
                $query->whereIn('obj_type', array_values(STEWARD_PUSH_OBJ_TYPE));
            })->with('sourcePush:id,obj_title,obj_type,title')->limit(100)->get()->toArray();
        if (empty($data)) {
            return;
        }

        $templateArr = [
            STEWARD_PUSH_OBJ_TYPE['project'] => SMS_TEMPLATE['steward_push_project'],
            STEWARD_PUSH_OBJ_TYPE['industry'] => SMS_TEMPLATE['steward_push_information_industry'],
            STEWARD_PUSH_OBJ_TYPE['meeting'] => SMS_TEMPLATE['steward_push_information_meeting'],
        ];

        foreach ($data as $key => $val) {

            $template = array_get($templateArr, $val['source_push']['obj_type']);
            $name = empty($val['enterprise_name']) ? trans('steward.default_enterprise_name') : $val['enterprise_name'];
            $arr = [
                'telephone' => $val['mobile'],
                'template' => $template,
                'param' => [
                    'enterprise_name' => getStrLength($name),
                    'title' => $val['source_push']['title'],
                ],
            ];
            // 这里保证每一条都能不能重发 所以 在这里容忍 更新
            app(SmsRepository::class)->send($arr);
            app(StewardPushRecordRepository::class)->updateRepository([
                'id' => $val['id'],
                'is_send' => 1,
            ]);
        }

        $this->send();
    }
}
