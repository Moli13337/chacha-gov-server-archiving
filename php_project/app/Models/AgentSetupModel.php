<?php
/**
 * Created by PhpStorm.
 * User: JP
 * Date: 2019/9/17
 * Time: 19:06
 */

namespace App\Models;


use App\Models\Log\AgentSetupLog;
use App\Repositories\CommonRepository;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentSetupModel extends BaseModel
{
    use SoftDeletes;
    use AgentSetupLog;

    // 表名
    protected $table = 'agent_setup';

    const TABLE_NAME = 'agent_setup';

    // 主键
    protected $primaryKey = 'id';

    // 黑名单
    protected $guarded = ['id'];

    public function file()
    {
        return $this->hasMany(AgentSetupFileModel::class,'agent_setup_id');
    }
}