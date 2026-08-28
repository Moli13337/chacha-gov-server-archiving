<?php

namespace App\Console\Commands;

use App\Models\Steward\StewardPushModel;
use App\Models\Steward\StewardPushRecordModel;
use App\Repositories\Steward\StewardPushRecordRepository;
use App\Repositories\User\UserPushRepository;
use Illuminate\Console\Command;

class FixStewardPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:stewardPush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->fix();
    }

    public function fix()
    {
        $push = StewardPushModel::get()->toArray();

        if (empty($push)) {
            return;
        }

        $push = array_column($push, null, 'id');

        $user = StewardPushRecordModel::get()->toArray();
        if (empty($user)) {
            return;
        }

        $data = [];
        foreach ($user as $k => $v) {
            $tmp = array_get($push, $v['steward_push_id'], []);
            if (empty($tmp)) {
                continue;
            }
            $data[] = [
                'user_id' => $v['user_id'],
                'obj_id' => $tmp['obj_id'],
                'obj_type' => $tmp['obj_type'],
                'created_at' => $tmp['created_at'],
                'updated_at' => $tmp['created_at'],
            ];
        }

        $res = app(UserPushRepository::class)->storeBatchRepository($data);
        dd($res);
    }
}
