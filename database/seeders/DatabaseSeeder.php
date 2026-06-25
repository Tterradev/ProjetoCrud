<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Ponto de entrada do `php artisan db:seed`. Aqui delegamos para os
     * seeders específicos. Para adicionar outros no futuro, é só incluí-los
     * na lista do call().
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
    }
}
