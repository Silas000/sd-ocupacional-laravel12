<?php

use App\Support\Cpf;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Declarada aqui porque a migradora injeta a saída de comando por
     * atributo dinâmico.
     */
    protected $command;

    /**
     * O CPF é dado pessoal (LGPD). Passamos a guardá-lo cifrado em repouso
     * e mantemos um HMAC determinístico apenas para unicidade e busca.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'cpf_hash')) {
                $table->string('cpf_hash', 64)->nullable()->after('cpf');
            }
        });

        $this->backfill();

        Schema::table('users', function (Blueprint $table) {
            $table->unique('cpf_hash');
        });
    }

    public function down(): void
    {
        $this->restorePlainCpf();

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['cpf_hash']);
            $table->dropColumn('cpf_hash');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('cpf', 255)->nullable()->change();
        });
    }

    /**
     * Cifra os CPFs já gravados e popula o HMAC correspondente.
     *
     * CPFs repetidos são inconsistência de cadastro, não motivo para
     * abortar a migração: o primeiro registro mantém o CPF e os demais
     * ficam sem hash para serem corrigidos, com o CPF legível no banco.
     */
    private function backfill(): void
    {
        $duplicados = [];

        DB::table('users')
            ->whereNotNull('cpf')
            ->where('cpf', '!=', '')
            ->orderBy('id')
            ->chunkById(200, function ($users) use (&$duplicados) {
                foreach ($users as $user) {
                    $plain = Cpf::decrypt($user->cpf) ?? $user->cpf;
                    $fingerprint = Cpf::fingerprint($plain);

                    $jaExiste = $fingerprint !== null && DB::table('users')
                        ->where('cpf_hash', $fingerprint)
                        ->where('id', '<', $user->id)
                        ->exists();

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'cpf' => Cpf::encrypt($plain),
                            'cpf_hash' => $jaExiste ? null : $fingerprint,
                        ]);

                    if ($jaExiste) {
                        $duplicados[] = $user->id;
                    }
                }
            });

        if ($duplicados !== []) {
            $this->warn(
                'CPF repetido encontrado nos usuários '.implode(', ', $duplicados).'. '
                .'O CPF foi mantido legível, mas sem hash de unicidade: corrija o cadastro.'
            );
        }
    }

    private function restorePlainCpf(): void
    {
        DB::table('users')
            ->whereNotNull('cpf')
            ->orderBy('id')
            ->chunkById(200, function ($users) {
                foreach ($users as $user) {
                    $plain = Cpf::decrypt($user->cpf) ?? $user->cpf;

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['cpf' => Cpf::normalize($plain)]);
                }
            });
    }

    private function warn(string $mensagem): void
    {
        $this->command?->warn($mensagem);

        Log::warning($mensagem);
    }
};
