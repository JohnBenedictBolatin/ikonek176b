<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SetSessionCookie
{
    /**
     * Map logical guard names to cookie names.
     */
    protected $map = [
        'admin' => 'admin_session',
        'administrator' => 'admin_session',
        'treasurer' => 'admin_session',
        'approver' => 'admin_session',

        'user' => 'user_session',
        'resident' => 'user_session',
        'employee' => 'user_session',
    ];

    /**
     * Handle an incoming request.
     *
     * Priority:
     * 1) X-Auth-Guard header (per-tab header set by the front-end)
     * 2) login_for request input (from forms)
     * 3) existing cookie presence ('admin_session' or 'user_session')
     * 4) fallback to default ('user_session')
     */
    public function handle(Request $request, Closure $next)
    {
        $cookieName = null;

        // 1) header from JS (preferred)
        $header = $request->header('X-Auth-Guard');
        if ($header) {
            $key = Str::lower(trim($header));
            if (isset($this->map[$key])) {
                $cookieName = $this->map[$key];
            } else {
                $cookieName = in_array($key, ['admin_session', 'user_session']) ? $key : null;
            }
        }

        // 2) login_for input on form submit
        if (! $cookieName && $request->filled('login_for')) {
            $lf = Str::lower(trim($request->input('login_for')));
            if (isset($this->map[$lf])) {
                $cookieName = $this->map[$lf];
            }
        }

        // 3) If cookies already exist, try to detect which to use.
        if (! $cookieName) {
            if ($request->cookies->has('admin_session')) {
                $cookieName = 'admin_session';
            } elseif ($request->cookies->has('user_session')) {
                $cookieName = 'user_session';
            } elseif ($request->cookies->has(config('session.cookie'))) {
                // map existing default cookie to user_session to be safe
                $cookieName = 'user_session';
            }
        }

        // 4) default fallback
        $cookieName = $cookieName ?? 'user_session';

        // Set the runtime session cookie name so StartSession will use this cookie.
        config(['session.cookie' => $cookieName]);

        return $next($request);
    }
}
