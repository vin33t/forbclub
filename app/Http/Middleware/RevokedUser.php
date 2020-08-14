<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class RevokedUser
{
  /**
   * Handle an incoming request.
   *
   * @param \Illuminate\Http\Request $request
   * @param \Closure $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    if (Auth::user()) {
      $user = Auth::user();
      if ($user->login_revoked) {

        Auth::logout();
        return Redirect::route('login', ['revoked'])->with(['message' => 'Login Revoked']);
      }
    }

    return $next($request);
  }
}
