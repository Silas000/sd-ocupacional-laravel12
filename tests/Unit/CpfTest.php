<?php

namespace Tests\Unit;

use App\Support\Cpf;
use Tests\TestCase;

class CpfTest extends TestCase
{
    public function test_aceita_cpfs_validos(): void
    {
        $this->assertTrue(Cpf::isValid('529.982.247-25'));
        $this->assertTrue(Cpf::isValid('52998224725'));
        $this->assertTrue(Cpf::isValid(' 529 982 247 25 '));
    }

    public function test_rejeita_cpfs_invalidos(): void
    {
        $this->assertFalse(Cpf::isValid('529.982.247-26'), 'dÃ­gito verificador errado');
        $this->assertFalse(Cpf::isValid('111.111.111-11'), 'CPF de dÃ­gitos repetidos');
        $this->assertFalse(Cpf::isValid('123'), 'tamanho incorreto');
        $this->assertFalse(Cpf::isValid(''), 'vazio');
        $this->assertFalse(Cpf::isValid(null), 'nulo');
    }

    public function test_normaliza_e_formata(): void
    {
        $this->assertSame('52998224725', Cpf::normalize('529.982.247-25'));
        $this->assertSame('529.982.247-25', Cpf::format('52998224725'));
        $this->assertNull(Cpf::normalize('---'));
    }

    public function test_fingerprint_e_deterministico_e_nao_reversivel(): void
    {
        $a = Cpf::fingerprint('529.982.247-25');
        $b = Cpf::fingerprint('52998224725');

        $this->assertSame($a, $b, 'a mesma entrada gera o mesmo hash mesmo com mÃ¡scara diferente');
        $this->assertNotSame('52998224725', $a, 'o hash nÃ£o revela o CPF');
        $this->assertSame(64, strlen($a));
        $this->assertNotSame($a, Cpf::fingerprint('11111111111'));
    }
}
