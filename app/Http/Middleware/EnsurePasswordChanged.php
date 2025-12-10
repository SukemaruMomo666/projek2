<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Jika user belum login, biarkan lanjut (nanti ditangani auth middleware)
        if (! $user) {
            return $next($request);
        }

        // Cek apakah password sudah diganti
        // Jika kolom is_password_changed = false (0)
        // DAN user tidak sedang mengakses halaman first-login atau logout
        if (! $user->is_password_changed && 
            ! $request->routeIs('first-login.*') && 
            ! $request->routeIs('logout')) {
            
            return redirect()->route('first-login.show');
        }

        return $next($request);
    }
}