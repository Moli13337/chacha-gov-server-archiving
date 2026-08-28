<?php

namespace App\Http\Middleware;

use App\Common\Code;
use App\Models\StaffBindDepartmentModel;
use App\Repositories\Staff\StaffDepartmentRepository;
use Closure;

class EnterpriseServiceCenter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $department = app(StaffDepartmentRepository::class)->departmentDetail(['type' => DEPARTMENT_TYPE['one']]);
        $staff = StaffBindDepartmentModel::where([
            'department_id' => $department['id'],
            'opertor_type' => STAFF_OPERTOR_TYPE['one'],
        ])->first();
        $id = (int)getLoginStaff('id');
        if (empty($staff) || $staff['staff_id'] != $id) {
//            return codeRender(Code::APPLY_CORRECT_DEPARTMENT_OPERATOR_ERROR);
        }

        return $next($request);
    }
}
