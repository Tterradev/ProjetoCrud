<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Guarda o hash da senha entre os registros para não recalcular o bcrypt
     * (que é propositalmente lento) a cada usuário gerado.
     */
    protected static ?string $password;

    /**
     * Define o estado padrão do model (o "molde" de um usuário fake).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // substr garante que o nome respeite o limite de 30 chars da migration
            'name' => substr(fake()->name(), 0, 30),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }
}
