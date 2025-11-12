<?php
 
namespace App\Http\Middleware;
 
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Cek apakah user punya role yang sesuai
        if ($request->user()->role !== $role) {
            // Jika tidak sesuai, lempar error 403 (Forbidden) atau redirect
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
 
        return $next($request);
    }
}
 