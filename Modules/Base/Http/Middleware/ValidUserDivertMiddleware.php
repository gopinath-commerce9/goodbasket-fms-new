<?php

namespace Modules\Base\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class ValidUserDivertMiddleware
{

    /**
     * Where to redirect admins after login.
     *
     * @var string
     */
    protected $redirectPass = '/dashboard';

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {

        switch($guard){
            case 'auth-user':
                if (Auth::guard($guard)->check()) {
                    return redirect($this->redirectPass);
                }
                break;
            default:
                $next($request);
                break;
        }

        return $next($request);

    }
}
