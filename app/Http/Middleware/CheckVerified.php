<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Untuk mencegah distributor yang belum diverifikasi admin mengakses
 * fitur-fitur yang memerlukan harga grosir (misalnya checkout).
 */
class CheckVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'distributor' && !$user->is_verified) {
            return redirect()
                ->route('shop.index')
                ->with('error', 
                    'Akses ditolak. Akun distributor Anda belum diverifikasi admin.' . 'Silahkan hubungi admin untuk percepat verifikasi.'
                );
        }

        return $next($request);
    }
}
