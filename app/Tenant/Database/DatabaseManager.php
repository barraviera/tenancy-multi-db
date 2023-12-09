<?php

namespace App\Tenant\Database;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

class DatabaseManager
{

    // criamos este metedo que recebe por parametro um objeto Company
    public function createDatabase(Company $company){

        // usamos a facade DB para manipular bancos
        DB::statement("
            CREATE DATABASE {$company->db_database} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
        ");

    }

}