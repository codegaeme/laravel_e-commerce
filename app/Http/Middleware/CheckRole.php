<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ sửa import
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|string|null  $roleId  // optional: nhận role id từ route
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $roleId = [1, 3, 4];

        foreach ($roleId as $value) {
            if (Auth::check() && Auth::user()->role?->id == $value) {
                return $next($request); // cho qua
            }
        }


        return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập!');
    }
}
