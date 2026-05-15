<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dependent;

class CheckDependentEligibility
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Semak akaun tanggungan yang sudah tidak layak
        |--------------------------------------------------------------------------
        */
        if ($user->account_type === 'tanggungan' && $user->linked_dependent_id) {
            $dependent = Dependent::find($user->linked_dependent_id);

            if ($dependent && $dependent->status_tanggungan === 'tidak_layak') {
                if (
                    !$request->routeIs('user.upgrade-membership.*') &&
                    !$request->routeIs('logout')
                ) {
                    return redirect()
                        ->route('user.upgrade-membership.create')
                        ->with('warning', 'Akaun tanggungan anda tidak lagi layak. Sila mohon naik taraf sebagai ahli utama.');
                }
            }
        }

        return $next($request);
    }
}