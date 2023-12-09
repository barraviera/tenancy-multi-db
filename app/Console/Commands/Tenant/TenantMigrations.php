<?php

namespace App\Console\Commands\Tenant;

use App\Models\Company;
use App\Tenant\ManagerTenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TenantMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // tenants:migrations este comando é para rodar as migrations que estao dentro da pasta database>migrations>tenant
    // especificamos {--refresh} para ser um parametro opcional .obs --refresh é um option
    // e indicamos {id?} que é um argumento pois poderemos rodar a migration somente para o id do tenant indicado
    protected $signature = 'tenants:migrations {id?} {--refresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations tenants';

    // criamos uma variavel privada para receber o $tenant do construtor
    private $tenant;

    // injetamos o model ManagerTenant no contrutor do comando
    public function __construct(ManagerTenant $tenant)
    {
        parent::__construct();

        $this->tenant = $tenant;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        

        // recuperar o argumento id caso ele tenha sido informado na linha de comando para rodar as migrations de tenant
        // se existe id entraremos neste bloco
        if( $id = $this->argument('id') ){
            // vamos localizar a empresa pelo id informado
            $company = Company::find($id);

            // se encontrou a empresa pelo id iremos executar o comando, senao iremos sair do bloco com return
            if($company)
                // chamamos o metodo execCommand que criamos abaixo
                $this->execCommand($company);
            return;
        }

        // pegamos todas as empresas
        $companies = Company::all();     
        
        // vamos percorre-las e setar a conexao para cada uma delas
        foreach($companies as $company){

            // chamamos o metodo execCommand que criamos abaixo
            $this->execCommand($company);

        }
    }

    public function execCommand(Company $company)
    {
        // se foi digitado --refresh iremos atribuia à variavel $command este valor 'migrate:refresh'
        // caso contrario atribuiremos 'migrate'
        $command = $this->option('refresh') ? 'migrate:refresh' : 'migrate';

        // setar conexao
        $this->tenant->setConnection($company);

        // log somente para visualizar qual empresa esta rodando no momento
        $this->info("Connecting Company {$company->name}");
        
        // o force pra forçar que rode este comando $command recebido la de cima
        // e iremos indicar o path que é o caminho onde estao as tabelas para as empresas/tenants
        Artisan::call($command, [
            '--force' => true,
            '--path' => '/database/migrations/tenant',
        ]);

        // log
        $this->info("End Connecting {$company->name}");
        $this->info("-------------------------------");
    }

}
