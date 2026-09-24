# Sistema de Saúde Ocupacional

**Créditos:** Programador Silas Rosário  
[LinkedIn](https://www.linkedin.com/in/silas-rosario/)

---

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
- **Linguagem:** PHP 8.x
- **Framework:** Laravel 10.x
- **Banco de Dados:** MySQL 8.x
- **Frontend:** Blade Templates, HTML5, CSS3, JavaScript
- **Autenticação:** Laravel Breeze
- **APIs:** Laravel API para integração futura

### Estrutura de Rotas
O sistema utiliza o arquivo `routes/web.php` para definir as rotas principais. As rotas são protegidas por middlewares que garantem autenticação e autorização baseadas em papéis (admin, médico, técnico).

Exemplo de rotas:
- `/dashboard`: Página inicial após login, acessível a todos os usuários autenticados.
- `/profile`: Gerenciamento do perfil do usuário.
- `/users`: Gerenciamento de usuários (somente administradores).
- `/exams`: Gerenciamento de exames médicos (administradores e médicos).
- `/risks`: Gerenciamento de riscos ocupacionais (administradores e técnicos).

---

## Funcionalidades

### Principais Funcionalidades
1. **Autenticação e Autorização**
   - Login, logout e recuperação de senha.
   - Controle de acesso baseado em papéis (admin, médico, técnico).

2. **Gestão de Usuários**
   - CRUD completo para usuários (somente administradores).

3. **Gestão de Exames**
   - Cadastro, edição e visualização de exames médicos (admin e médicos).

4. **Gestão de Riscos**
   - Cadastro e monitoramento de riscos ocupacionais (admin e técnicos).

5. **Gestão de Incidentes**
   - Registro e acompanhamento de incidentes no ambiente de trabalho (admin e técnicos).

6. **Gestão de Perfis**
   - Atualização e exclusão de perfis de usuários.

---

## Instalação e Configuração

### Requisitos Mínimos
- **Hardware:**
  - Processador: Dual Core 2.0 GHz ou superior
  - Memória RAM: 4 GB
  - Armazenamento: 500 MB livres
- **Software:**
  - PHP 8.x
  - Composer
  - MySQL 8.x
  - Servidor Web (Apache ou Nginx)

### Passos para Instalação
1. Clone o repositório:
   ```bash
   git clone <URL_DO_REPOSITORIO>

2. Acesse o diretório do projeto:
   ```bash
   cd saude-ocupacional
   ```
3. Instale as dependências do projeto:
   ```bash
   composer install
   ```
4. Copie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente:
   ```bash
   cp .env.example .env
   ```
5. Gere a chave da aplicação:
   ```bash
   php artisan key:generate
   ```
6. Configure o banco de dados no arquivo `.env` e execute as migrações:
   ```bash
   php artisan migrate
   ```
7. Inicie o servidor de desenvolvimento:
   ```bash
   php artisan serve
   ```

Agora, o sistema estará disponível no endereço `http://localhost:8000`.

---

## Logs e Monitoramento

- Os logs do sistema estão localizados em `storage/logs/laravel.log`.
- Utilize ferramentas como Laravel Telescope para monitorar o sistema.

---

## Segurança

### Autenticação e Autorização
- O sistema utiliza middleware para proteger rotas e garantir acesso baseado em papéis.

### Proteção de Dados
- Dados sensíveis são armazenados de forma segura no banco de dados.
- Utilize HTTPS para proteger a comunicação.

### Boas Práticas Recomendadas
- Atualize regularmente as dependências.
- Monitore os logs para identificar atividades suspeitas.