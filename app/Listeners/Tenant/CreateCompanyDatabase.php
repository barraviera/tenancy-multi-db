<?php

namespace App\Listeners\Tenant;

use App\Events\Tenant\CompanyCreated;
use App\Tenant\Database\DatabaseManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DatabaseManager as DatabaseDatabaseManager;
use Illuminate\Queue\InteractsWithQueue;

class CreateCompanyDatabase
{
    private $database;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(DatabaseManager $database)
    {
        $this->database = $database;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\Tenant\CompanyCreated  $event
     * @return void
     */
    public function handle(CompanyCreated $event)
    {
        // no event temos o metodo company que criamos em CompanyCreated.php dentro de App>Events>Tenant
        $company = $event->company();

        // como a criacao de um banco de dados para o tenant é uma coisa complexa entao criar em App>Tenant>Database
        // o arquivo DatabaseManager e nele terá a logica de criacao do banco e aqui só iremos chamar este arquivo
        $this->database->createDatabase($company);
    }
}
