# Sistema de Saúde Ocupacional

**Créditos:** Programador Silas Rosário  
[LinkedIn](https://www.linkedin.com/in/silas-rosario/)

---

## Capturas de Tela

### Página de Login
![Página de Login](https://i.imgur.com/D34T8ul.png)

### Tela Dashboard Administrativo
![Tela Dashboard Administrativo](https://i.imgur.com/ICEeK18.png)

### Tela de Exames
![Tela de Exames](https://i.imgur.com/WZrMIaX.png)

### Tela de Usuários
![Tela de Usuários](https://i.imgur.com/DaENRM5.png)

### Tela de Personalização do Sistema
![Tela de Personalização do Sistema](https://i.imgur.com/suiTqGn.png)

## Visão Geral

O **Sistema de Saúde Ocupacional** é uma aplicação web desenvolvida para gerenciar e monitorar informações relacionadas à saúde e segurança no trabalho. Ele oferece funcionalidades como controle de exames médicos, incidentes, riscos ocupacionais e gestão de usuários, com foco em atender empresas e profissionais da área de saúde ocupacional.

### Objetivo
Automatizar e centralizar os processos relacionados à saúde ocupacional, garantindo maior eficiência, segurança e conformidade com normas regulamentadoras.

### Público-Alvo
- Empresas que precisam gerenciar a saúde ocupacional de seus colaboradores.
- Profissionais da área de saúde ocupacional, como médicos, técnicos e administradores.

---

## Arquitetura

### Tecnologias Utilizadas
- **Linguagem:** PHP 8.2 ou superior
- **Framework:** Laravel 12.x
- **Banco de Dados:** MySQL 8.x
- **Frontend:** Blade Templates, Tailwind CSS, JavaScript (Chart.js)
- **Autenticação:** Laravel Breeze
- **Qualidade:** PHPUnit / Pest runner do Laravel, Laravel Pint

### Papéis de acesso

| Papel        | Área de saúde (exames, prontuário) | Área de segurança (riscos, ocorrências) | Usuários | Auditoria |
|--------------|:-----------------------------------:|:----------------------------------------:|:--------:|:---------:|
| `admin`      | ✔                                   | ✔                                        | ✔       | ✔         |
| `medico`     | ✔                                   | —                                        | —       | —         |
| `tecnico`    | —                                   | ✔                                        | —       | —         |
| `funcionario`| apenas os próprios dados            | apenas os próprios dados                 | —       | —         |

A autorização é feita por **Policies** (`app/Policies`) combinadas com o middleware
`check.role`, que restringe o grupo de rotas.

### Estrutura de Rotas
O sistema utiliza `routes/web.php`, que exige `auth` e `force.password` para todo o
acesso autenticado, e `routes/auth.php` para o fluxo de sessão.

- `/dashboard`: página inicial após login, com visão específica por papel.
- `/profile`: gerenciamento do perfil e da senha do próprio usuário.
- `/force-password-change`: troca obrigatória de senha no primeiro acesso.
- `/exams`, `/health`: exames e prontuário (admin e médico).
- `/risks`, `/incidents`: riscos e ocorrências (admin e técnico).
- `/users`: gestão de usuários (somente admin).
- `/audits`: trilha de auditoria (somente admin).
- `/relatorios`: relatórios com exportação em PDF e Excel (admin, médico e técnico).
- `/settings`: personalização visual do sistema (somente admin).

> **Não existe auto-cadastro.** Toda conta é criada por um administrador e nasce com
> `must_change_password = true`.

---

## Funcionalidades

1. **Autenticação e autorização**
   - Login, logout, recuperação e redefinição de senha.
   - Troca obrigatória de senha no primeiro acesso, aplicada a todas as rotas.
   - Bloqueio das 5 últimas senhas por usuário e senha mínima com maiúscula, minúscula, número e símbolo.
   - Limite de tentativas no login.

2. **Gestão de usuários** (somente administradores)
   - CRUD completo, com validação de CPF (dígitos verificadores) e unicidade.
   - Guardas: ninguém exclui a própria conta, ninguém remove o próprio acesso de admin e o sistema sempre preserva ao menos um admin.

3. **Gestão de exames** (admin e médico)
   - Cadastro, edição, visualização e exclusão lógica.
   - Status, tipo e severidade validados por *backed enums* (`app/Enums`), alinhados com os enums do banco.
   - Vencimento calculado pela periodicidade do tipo de exame.

4. **Gestão de riscos** (admin e técnico)
   - Cadastro por setor, com severidade, categoria e medidas preventivas.

5. **Gestão de incidentes** (admin e técnico)
   - Registro com tipo, severidade, local e medidas corretivas.

6. **Prontuário de saúde** (admin e médico)
   - Registros de saúde vinculados a colaboradores e a exames.

7. **Auditoria**
   - Toda criação, alteração, exclusão e restauração é registrada em `audits` com autor, data, IP, URL e os valores anterior e novo.
   - Senhas e CPF nunca são gravados em claro na auditoria.

8. **Relatórios exportáveis** (admin, médico e técnico)
   - Cinco relatórios — Exames, Histórico de saúde, Riscos, Ocorrências e Colaboradores — com os **mesmos filtros das listagens**, então tela e relatório mostram o mesmo recorte.
   - Exportação em **PDF** (Dompdf, cabeçalho da tabela repetido em cada página) e **Excel** (OpenSpout, com autofiltro, linha de cabeçalho fixa e linhas de identificação do relatório).
   - Quem não abre uma área também não a exporta: os relatórios espelham a divisão de papéis de `admin`, `medico` e `tecnico`, e um relatório fora do papel do usuário devolve 404 em vez de 403.
   - Somente o administrador pode incluir registros excluídos logicamente no recorte.
   - **Toda exportação é registrada na auditoria** com relatório, formato, quantidade de registros e filtros usados.
   - CPF nunca entra em relatório.

9. **Personalização visual** (somente admin, em `/settings`)
   - Nome do sistema, subtítulo, logotipo (PNG/JPG/WebP até 2 MB), cor do menu, cor de destaque, fundo da tela de login e ícone de cada item do menu.
   - Cores e ícones vêm de um **catálogo fechado** em `app/Support/Theme.php`: o administrador escolhe entre opções conhecidas, nunca informa uma classe CSS ou uma URL arbitrária, o que mantém XSS fora do alcance de uma conta comprometida.
   - As configurações ficam em cache e são invalidadas a cada gravação; a tela já falha sozinha para a paleta padrão se um valor gravado estiver corrompido.
   - Cada alteração é auditada.

---

## Segurança e privacidade

Este sistema trata de **dados sensíveis de saúde ocupacional** (Lei nº 13.709/2018 — LGPD).
As medidas implementadas:

| Tema | Implementação |
|---|---|
| Registro público | Removido; contas criadas somente por admin |
| Troca obrigatória de senha | Middleware `force.password` em todo o grupo autenticado |
| Reuso de senha | Tabela `password_history`, últimas 5 senhas bloqueadas |
| CPF | Cifrado em repouso (`Crypt`) + HMAC `cpf_hash` para unicidade sem expor o valor |
| Exclusão de dados | Exclusão lógica (`deleted_at`) em exames, prontuário, riscos, ocorrências e usuários |
| Integridade do histórico | Excluir um usuário não apaga seu histórico de saúde |
| Rastreabilidade | Trilha de auditoria em `audits` |
| Autorização | Policies por recurso, com checagem de posse do registro |
| Configurações | Catálogo fechado de cores e ícones; nada de CSS ou URL livre |
| Saída de dados | Toda exportação em PDF/Excel registrada na auditoria, com o recorte usado |
| Senha do admin inicial | Vem de `ADMIN_PASSWORD` no `.env`, nunca versionada |

**Pontos que exigem configuração no ambiente de produção:**

- `SESSION_ENCRYPT=true` e `SESSION_LIFETIME` curto (o `.env.example` já vem assim; confira o `.env` em produção).
- `APP_DEBUG=false`.
- Uso obrigatório de HTTPS.
- `APP_KEY` precisa ser preservada: trocá-la torna ilegíveis os CPFs já cifrados. Use `php artisan key:rotate` para rotacionar.
- Backup do banco, incluindo a tabela `audits`.

---

## Instalação e Configuração

### Requisitos Mínimos
- **Hardware:** Dual Core 2.0 GHz ou superior, 4 GB de RAM, 500 MB livres
- **Software:** PHP 8.2+, Composer, MySQL 8.x, Apache ou Nginx

### Passos para Instalação
1. Clone o repositório:
   ```bash
   git clone <URL_DO_REPOSITORIO>
   ```

2. Acesse o diretório do projeto:
   ```bash
   cd saude-ocupacional
   ```

3. Instale as dependências:
   ```bash
   composer install
   ```

4. Crie o arquivo de ambiente:
   ```bash
   cp .env.example .env
   ```

5. Gere a chave da aplicação:
   ```bash
   php artisan key:generate
   ```

6. Configure no `.env` o banco de dados e **defina `ADMIN_PASSWORD`** com uma senha forte (mínimo 8 caracteres, com maiúscula, minúscula, número e símbolo).

7. Crie o banco de testes, usado pela suíte:
   ```sql
   CREATE DATABASE saude_ocupacional_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

8. Exponha o diretório de uploads, necessário para o logotipo configurado em `/settings`:
   ```bash
   php artisan storage:link
   ```

9. Execute as migrações e crie o administrador inicial:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
   O `db:seed` só cria o administrador se `ADMIN_PASSWORD` estiver preenchida no `.env`.

10. Inicie o servidor de desenvolvimento:
    ```bash
    npm install
    npm run build      # ou: npm run dev
    php artisan serve
    ```

O sistema estará disponível em `http://localhost:8000`. Faça login com `ADMIN_EMAIL` /
`ADMIN_PASSWORD`; a troca de senha será exigida imediatamente.

> **Nota sobre CPF duplicado:** a migração que cifra o CPF detecta valores repetidos e
> mantém o CPF legível, mas **sem** o hash de unicidade, registrando um aviso no log.
> Corrija o cadastro dos usuários apontados — a validação impede novos duplicados.

---

## Testes

```bash
php artisan test
```

A suíte roda contra o banco `saude_ocupacional_test` (configurado em `phpunit.xml`) e
cobre autenticação, troca obrigatória de senha, política de senhas, matrix de papéis,
validação de enums, exclusão lógica, trilha de auditoria, CPF, personalização visual e
relatórios — incluindo a leitura do `.xlsx` gerado, para garantir que o arquivo sai
correto.

Formato e estilo:

```bash
vendor\bin\pint
```

---

## Logs e Monitoramento

- Logs da aplicação em `storage/logs/laravel.log`.
- Consultas e alterações de dados de saúde podem ser acompanhadas pela tela `/audits`.

---

## Melhorias Implementadas

### Escala e desempenho
- **Paginação, busca e filtros** nas cinco listagens (exames, prontuário, riscos, ocorrências, usuários), com `withQueryString()` para preservar o filtro ao trocar de página.
- **Busca por CPF** funciona apesar do valor estar cifrado: a consulta compara o HMAC (`cpf_hash`), não o dado em claro.
- **Índices compostos** nos campos mais consultados por filtros e dashboards.
- **Métricas dos dashboards** extraídas para `App\Services\DashboardPresenter`; o `DashboardController` apenas escolhe a view.
- O gráfico de ocorrências sempre devolve os 12 meses, evitando eixo desalinhado em meses sem registro.
- Frontend servido pelo bundle do Vite, **sem CDN em tempo de execução** (Chart.js vem por npm).

### Manutenibilidade
- Backed enums em `app/Enums` validando `status`, `tipo`, `severidade`, `categoria` e `role`, alinhados com os enums do banco.
- Form Requests (`app/Http/Requests`) no lugar de arrays de validação duplicados em cada controller.
- Scopes de filtro nos models, com as regras de busca fora dos controllers.
- Componentes Blade reutilizáveis: `<x-badge>`, `<x-filter-bar>`, `<x-pagination-summary>`, `<x-icon>`, `<x-menu-item>`, `<x-menu-lateral>`.
- Relatórios declarativos: cada um é uma `App\Support\ReportDefinition` com papéis, filtros e colunas, e a mesma implementação serve a tela, o PDF e o Excel. Acrescentar um relatório é adicionar uma entrada no `App\Services\ReportRegistry`.
- Cores e ícones de Configurações vivem em um único lugar (`app/Support/Theme.php`), que o Tailwind varrido via `content` no `tailwind.config.js` — sem isso as classes da paleta não seriam geradas, porque não aparecem literais em nenhum Blade.

---

## Melhorias Planejadas

- Notificação por e-mail de exames vencidos
- Mapa de riscos do técnico de segurança
- Central de notificações para o colaborador
