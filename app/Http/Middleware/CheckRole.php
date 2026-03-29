<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Supports single role ('admin') and comma-separated list ('admin,reception').
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Flatten roles — support both variadic args and comma-separated string
        $allowed = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $allowed[] = trim($r);
            }
        }

        if (! in_array(auth()->user()->role, $allowed)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
