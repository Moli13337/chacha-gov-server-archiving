<?php

namespace App\Http\Middleware;

use App\Repositories\Staff\StaffRepository;
use Closure;

class AdminDepartment
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
        $id = (int)getLoginStaff('id');
        $result = app(StaffRepository::class)->getDepartment($id);
        setLoginDepartment($result);
        return $next($request);
    }
}
