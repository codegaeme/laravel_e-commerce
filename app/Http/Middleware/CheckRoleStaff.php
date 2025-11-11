<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class CheckRoleStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next, $roleId ): Response
    {
        $roleId = 1;
        if (Auth::check() && Auth::user()->role?->id == $roleId) {
            return $next($request); // cho qua
        }

        return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập!');
    }
}
