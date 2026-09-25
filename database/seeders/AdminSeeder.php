<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');

        if (! is_string($password) || strlen($password) < 8) {
            $this->command?->warn('ADMIN_PASSWORD ausente ou com menos de 8 caracteres: o admin padrão não foi criado.');

            return;
        }

        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@saudeocupacional.com')],
            [
                'name' => 'Administrador',
                'password' => Hash::make($password),
                'role' => UserRole::Admin->value,
                'must_change_password' => true,
            ]
        );
    }
}
