<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Sesi tidak valid, silakan login kembali'
            ], 401);
        }

        if (!in_array($request->user()->role, $roles)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Akses ditolak. Peran Anda (' . $request->user()->role . ') tidak memiliki hak akses ke endpoint ini.'
            ], 403);
        }

        return $next($request);
    }
}
