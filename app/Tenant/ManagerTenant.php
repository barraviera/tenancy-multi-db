<?php

namespace App\Tenant;

use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ManagerTenant
{

    // metodo para setar conexao recebendo por parametro os dados da empresa
    public function setConnection(Company $company)
    {
        // remover conexoes abertas
        // veja que passamos a conexao tenant que está em config>database.php
        DB::purge('tenant');

        // setar novos dados de configução para a conexao tenant que criamos em config>database.php
        config()->set('database.connections.tenant.host', $company->db_hostname);
        config()->set('database.connections.tenant.database', $company->db_database);
        config()->set('database.connections.tenant.username', $company->db_username);
        config()->set('database.connections.tenant.password', $company->db_password);
        // para reconectar com as novas configurações que informamos
        DB::reconnect('tenant');

        // testar conexao pingando nela, se nao der erro ele nao retornar nada
        // se der erro retorna uma exception
        Schema::connection('tenant')->getConnection()->reconnect();
    }

    // se for o dominio principal retorna true senao retorna false
    public function domainIsMain()
    {
        // este request é o mesmo que $request porem ele é um helper
        //entao se request()->getHost() que é a nossa url atual
        // for igual ao dominio main que indicamos em config>tenant.php...
        return request()->getHost() == config('tenant.domain_main');
    }


}