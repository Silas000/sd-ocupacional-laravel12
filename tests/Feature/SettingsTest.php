<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Audit;
use App\Models\Setting;
use App\Models\User;
use App\Services\AppSettings;
use App\Support\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * A tela de configuração é global e afeta todos os usuários, então só o
 * administrador a abre, e cada mudança precisa ficar na auditoria.
 */
class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_apenas_administrador_acessa_a_configuracao(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/settings')
            ->assertOk()
            ->assertSee('Aparência do sistema');

        foreach ([UserRole::Medico, UserRole::Tecnico, UserRole::Funcionario] as $papel) {
            $this->actingAs(User::factory()->create(['role' => $papel->value]))
                ->get('/settings')
                ->assertForbidden();
        }
    }

    public function test_configuracoes_salvas_valem_na_tela_e_no_login(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put('/settings', $this->payload([
            'app_name' => 'SST Corporativo',
            'app_tagline' => 'Gestão de segurança e saúde',
            'nav_color' => 'navy',
            'accent_color' => 'emerald',
            'login_background' => 'dark',
        ]))->assertRedirect('/settings');

        $this->assertDatabaseHas('settings', ['key' => AppSettings::APP_NAME, 'value' => 'SST Corporativo']);
        $this->assertDatabaseHas('settings', ['key' => AppSettings::NAV_COLOR, 'value' => 'navy']);

        $this->actingAs($admin)->get('/dashboard')->assertOk()->assertSee('SST Corporativo');

        $this->post('/logout');

        $this->get('/')->assertOk()->assertSee('SST Corporativo')->assertSee('Gestão de segurança e saúde');
    }

    public function test_mudanca_de_configuracao_e_auditada(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put('/settings', $this->payload(['app_name' => 'Nome Auditado']))
            ->assertRedirect();

        $auditorias = Audit::query()
            ->where('auditable_type', (new Setting)->getMorphClass())
            ->orderBy('id')
            ->get();

        $this->assertNotEmpty($auditorias, 'a gravação da configuração deve ser auditada');

        $gravacao = $auditorias->firstWhere('event', 'created');

        $this->assertNotNull($gravacao);
        $this->assertSame($admin->id, $gravacao->user_id);
        $this->assertSame(AppSettings::APP_NAME, $gravacao->new_values['key'] ?? null);
        $this->assertSame('Nome Auditado', $gravacao->new_values['value'] ?? null);
    }

    public function test_nome_ficando_valido_e_obrigatorio(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->put('/settings', $this->payload(['app_name' => 'ab']))
            ->assertSessionHasErrors('app_name');
    }

    public function test_cores_fora_do_catalogo_sao_recusadas(): void
    {
        $admin = User::factory()->admin()->create();

        foreach (['nav_color', 'accent_color', 'login_background'] as $campo) {
            $this->actingAs($admin)
                ->put('/settings', $this->payload([$campo => 'qualquer-coisa']))
                ->assertSessionHasErrors($campo);
        }
    }

    public function test_classe_css_injetada_nao_e_aceita(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->put('/settings', $this->payload(['nav_color' => 'bg-red-500"><script>alert(1)</script>']))
            ->assertSessionHasErrors('nav_color');
    }

    public function test_icones_sao_persistidos_por_item(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put('/settings', $this->payload([
            'icons' => ['dashboard' => 'estrela', 'exams' => 'cadeado'],
        ]))->assertRedirect();

        $icones = app(AppSettings::class)->get(AppSettings::NAV_ICONS);

        $this->assertSame('estrela', $icones['dashboard']);
        $this->assertSame('cadeado', $icones['exams']);
        $this->assertSame('pulseira', $icones['health'], 'itens não enviados mantêm o padrão');
    }

    public function test_icone_desconhecido_e_recusado(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->put('/settings', $this->payload(['icons' => ['dashboard' => 'nao-existe']]))
            ->assertSessionHasErrors('icons.dashboard');
    }

    public function test_logotipo_e_enviado_e_exibido(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put('/settings', $this->payload(['logo' => UploadedFile::fake()->image('marca.png')]))
            ->assertRedirect();

        $caminho = app(AppSettings::class)->logoUrl();

        $this->assertNotNull($caminho);
        Storage::disk('public')->assertExists(app(AppSettings::class)->get(AppSettings::LOGO_PATH));

        $this->actingAs($admin)->get('/dashboard')->assertOk()->assertSee($caminho);
    }

    public function test_logotipo_pode_ser_removido_e_o_arquivo_apagado(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put('/settings', $this->payload(['logo' => UploadedFile::fake()->image('marca.png')]));

        $caminho = app(AppSettings::class)->get(AppSettings::LOGO_PATH);
        Storage::disk('public')->assertExists($caminho);

        $this->actingAs($admin)
            ->put('/settings', $this->payload(['remover_logo' => '1']))
            ->assertRedirect();

        $this->assertNull(app(AppSettings::class)->logoUrl());
        Storage::disk('public')->assertMissing($caminho);
    }

    public function test_configuracao_corrompida_cai_no_padrao(): void
    {
        Setting::create(['key' => AppSettings::NAV_COLOR, 'value' => 'cor-inexistente', 'type' => 'text']);
        Setting::create(['key' => AppSettings::NAV_ICONS, 'value' => '{quebrado', 'type' => 'array']);

        $settings = app(AppSettings::class);

        $this->assertSame(Theme::NAV['graphite'], $settings->nav());
        $this->assertSame('grade', $settings->icone('dashboard'));
    }

    /**
     * @param  array<string, mixed>  $extras
     * @return array<string, mixed>
     */
    private function payload(array $extras = []): array
    {
        return array_merge([
            'app_name' => 'Saúde Ocupacional',
            'app_tagline' => 'Sistema de Gestão de Saúde do Trabalhador',
            'nav_color' => 'graphite',
            'accent_color' => 'indigo',
            'login_background' => 'blue-light',
            'icons' => array_fill_keys(array_keys(app(AppSettings::class)->iconesPadrao()), 'grade'),
        ], $extras);
    }
}
