<?php

namespace App\Http\Middleware;

use Closure;

class CheckManager
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
        if (auth()->user()->is_manager !== 1) {
            echo "Bạn không có quyền truy cập vào chức năng này. Chức năng này chỉ dành cho Quản lý";die;
        }

        return $next($request);
    }
}
