<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const NOVO_ENUM = "ENUM('admin', 'medico', 'tecnico', 'funcionario') DEFAULT 'funcionario'";

    private const ANTIGO_ENUM = "ENUM('admin', 'rh', 'medico', 'tecnico') DEFAULT 'rh'";

    public function up(): void
    {
        $this->alterarRole(self::NOVO_ENUM);
    }

    public function down(): void
    {
        // 'funcionario' não existe no enum antigo. Converter explicitamente
        // evita que o MySQL trunque os valores silenciosamente.
        if ($this->suportaEnum()) {
            DB::table('users')->where('role', 'funcionario')->update(['role' => 'rh']);
        }

        $this->alterarRole(self::ANTIGO_ENUM);
    }

    private function alterarRole(string $definicao): void
    {
        if (! $this->suportaEnum()) {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role {$definicao}");
    }

    /**
     * Em SQLite e PostgreSQL a coluna se comporta como texto e a
     * restrição de valores é aplicada na validação, não no esquema.
     */
    private function suportaEnum(): bool
    {
        return in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true);
    }
};
