<?php

namespace App\Http\Middleware\Tenant;

use Closure;
use Illuminate\Http\Request;

class CheckDomainMain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // recuperar o dominio atual que está sendo acessado
        $current_domain = $request->getHost();

        // recuperar o dominio principal que está em config>tenant.php
        $main_domain = config('tenant.domain_main');

        // se o dominio atual que está sendo acessado for diferente do main que indicamos na configuracao...
        if( $current_domain != $main_domain ){
            // iremos abortar e passar um codigo 401 de nao autorizado
            abort(401);
        }

        return $next($request);
    }
}
