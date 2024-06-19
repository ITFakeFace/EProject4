<?php

namespace App\Http\Middleware;

use Closure;

class CheckHr
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
        if (auth()->user()->department !== 2 && auth()->user()->id != 7) {
            echo "Bạn không có quyền truy cập vào chức năng này. Chức năng này chỉ dành cho phòng Nhân Sự";die;
        }

        return $next($request);
    }
}
