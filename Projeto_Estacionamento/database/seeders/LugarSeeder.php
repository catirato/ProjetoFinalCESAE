<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LugarSeeder extends Seeder
{
    public function run(): void
    {
        // 10 lugares totais para manter 7 disponíveis quando 1, 2 e 5 são fixos.
        for ($numero = 1; $numero <= 10; $numero++) {
            DB::table('lugar')->updateOrInsert(
                ['numero' => $numero],
                ['ativo' => true]
            );
        }
    }
}
