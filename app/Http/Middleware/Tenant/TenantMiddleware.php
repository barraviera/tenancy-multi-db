<?php

namespace App\Http\Middleware\Tenant;

use App\Models\Company;
use App\Tenant\ManagerTenant;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TenantMiddleware
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
        $manager = app(ManagerTenant::class);

        // se o dominio for o principal damos um return next para continuar com a criacao
        if($manager->domainIsMain())
            return $next($request);

        // chamamos o metodo que criamos abaixo e passamos o host para ele
        // atribuímos o resultado em uma variavel $company
        $company = $this->getCompany($request->getHost());

        // !$company = se nao encontrou nada na tabela...
        // $request->url() != route('404.tenant') = e se a rota atual for diferente da 404
        if( !$company && $request->url() != route('404.tenant') ){
            // iremos redirecionar para a pagina de erro
            return redirect()->route('404.tenant');

        // $request->url() != route('404.tenant') = senao se nao tivermos ja na rota de 404
        // !$manager->domainIsMain() = e se o dominio nao for o principal, iremos setar a conexao com o banco do tenant
        }else if( $request->url() != route('404.tenant') && !$manager->domainIsMain() ){
            // mas se encontrou iremos setar a conexao usando a classe ManagerTenant que criarmos
            // app para criar um novo objeto ManagerTenant e passamos para ele os dados da empresa para criar a conexao
            $manager->setConnection($company);
        }                   

        return $next($request);
    }

    // criamos este metodo para recuperar a empresa
    // recebemos o dominio por parametro
    public function getCompany($host)
    {
        // vamos usar o model Company para buscar no banco de dados
        // onde o $host que recebemos seja igual a um dominio que está na tabela companies na coluna domain
        // first para retornar o primeiro resultado
        return Company::where('domain', $host)->first();
    }
}
