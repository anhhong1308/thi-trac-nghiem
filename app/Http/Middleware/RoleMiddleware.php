<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Chưa đăng nhập hoặc không đúng vai trò
        if (!$user || !in_array($user->role, $roles, true)) {
            abort(403);
        }

        // Giảng viên phải được Admin duyệt tài khoản
        if (
            $user->role === 'teacher'
            && $user->account_status !== 'approved'
        ) {
            abort(403, 'Tài khoản giảng viên của bạn chưa được Admin phê duyệt.');
        }

        return $next($request);
    }
}
