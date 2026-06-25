<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Um usuário fixo e conhecido — útil para você testar sempre com o
        // mesmo login. Só sobrescrevemos nome e email; a senha vem do padrão
        // da factory (o texto "password", já com hash).
        User::factory()->create([
            'name' => 'Pedro Teste',
            'email' => 'pedro.teste@example.com',
        ]);

        // 50 usuários aleatórios para testar listagem, paginação e a busca.
        // Mude o número se quiser mais/menos registros.
        User::factory()->count(50)->create();
    }
}
