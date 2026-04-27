# 🏥 Medic — Sistema de Prontuários Médicos Familiares

**Medic** é um sistema web completo para gerenciamento de prontuários médicos familiares, desenvolvido em **PHP** e **MySQL**. Permite que uma família organize e acompanhe todo o histórico de saúde de seus membros — consultas, exames, medicamentos, especialidades e arquivos — em um único lugar, com acesso seguro e controle de permissões.

---

## ✨ Funcionalidades

### 👥 Gestão de Pacientes
- Cadastro completo com dados pessoais, contato, convênio e foto
- Informações médicas: alergias, condições crônicas, tipo sanguíneo
- Vínculo familiar (pai, mãe, filho, cônjuge, etc.)
- Arquivos genéricos por paciente (qualquer documento extra, com comentários)
- Visualização completa do perfil com resumo de consultas, exames e medicamentos

### 📋 Prontuários Médicos
- Registro de consultas com data, médico, especialidade e diagnóstico
- Campo de motivo da consulta
- Anexo de múltiplos arquivos (imagens, PDFs, documentos, áudios, vídeos)
- Visualização rica com preview inline de cada tipo de arquivo
- Galeria de imagens com lightbox (navegação por setas)

### 🔬 Exames
- Registro de exames com tipo, data, laboratório, médico solicitante
- Suporte a múltiplas especialidades por exame (widget multi-seleção)
- Upload de arquivos de resultado (imagens, PDFs, etc.)
- Visualização categorizada por tipo de arquivo
- **Comparação lado a lado** entre exames do mesmo tipo
- Histórico de exames anteriores na visualização

### 💊 Medicamentos
- Cadastro com nome, princípio ativo, dosagem e frequência
- Classificação: contínuo ou temporário
- Controle de período (data início/fim)
- Status ativo/inativo
- Filtros por paciente e status

### 🗓️ Agenda / Planner
- Visualização mensal em calendário
- Eventos automáticos a partir de consultas, exames e medicamentos
- Navegação entre meses
- Indicadores visuais por tipo de evento

### 📅 Linha do Tempo
- Visualização cronológica unificada de todos os eventos do paciente
- Consultas, exames e medicamentos em uma timeline integrada
- Filtro por paciente

### 🩺 Especialidades Médicas
- Cadastro e gerenciamento de especialidades
- Widget de seleção com autocomplete e criação inline
- Suporte a múltiplas especialidades por exame

### 📊 Relatórios
- Dashboard com estatísticas gerais
- Relatórios consolidados por paciente, período e tipo

### 👤 Gestão de Usuários e Permissões
- Sistema de login com autenticação segura (bcrypt)
- Dois perfis: **Admin** e **Usuário comum**
- Admin: acesso total (CRUD completo)
- Usuário: visualização dos pacientes autorizados
- Controle de acesso por paciente (qual usuário vê qual paciente)
- Log de acessos ao sistema

### 📁 Gerenciamento de Arquivos
- Upload com barra de progresso (AJAX)
- Suporte a: imagens, PDFs, Word, áudios, vídeos e outros
- Preview inline por tipo de arquivo
- Download individual
- Galeria de imagens com lightbox e navegação
- Arquivos extras por paciente (com comentário opcional)

### 🔄 Exportação e Modo Local
- Exportação de dados para backup
- Modo local para operação offline

---

## 🛠️ Tecnologias

| Componente | Tecnologia |
|---|---|
| **Backend** | PHP 8+ |
| **Banco de Dados** | MySQL 5.7+ / MariaDB |
| **Frontend** | Bootstrap 5, Bootstrap Icons |
| **JavaScript** | Vanilla JS (sem frameworks) |
| **Autenticação** | Sessões PHP + bcrypt |
| **Servidor** | Apache com mod_rewrite |

---

## 📁 Estrutura do Projeto

```
medic/
├── index.php                  # Ponto de entrada (redirect para login/dashboard)
├── .htaccess                  # Regras Apache
├── config/
│   ├── database.php           # Configuração do banco de dados
│   └── database_local.php     # Config local (não versionado)
├── includes/
│   ├── auth.php               # Autenticação e controle de acesso
│   ├── functions.php          # Funções utilitárias
│   ├── header.php             # Template header + sidebar
│   └── footer.php             # Template footer
├── assets/
│   ├── css/style.css          # Estilos customizados
│   └── js/
│       ├── app.js             # JavaScript geral
│       ├── specialty-widget.js       # Widget de especialidade (single)
│       └── specialty-multi-widget.js # Widget de especialidade (multi)
├── pages/
│   ├── login.php              # Tela de login
│   ├── register.php           # Tela de registro
│   ├── logout.php             # Logout
│   ├── dashboard.php          # Dashboard principal
│   ├── profile.php            # Perfil do usuário
│   ├── timeline.php           # Linha do tempo
│   ├── planner.php            # Agenda/calendário
│   ├── patients/              # CRUD de pacientes + arquivos extras
│   ├── records/               # CRUD de prontuários
│   ├── exams/                 # CRUD de exames + comparação
│   ├── medications/           # CRUD de medicamentos
│   ├── specialties/           # CRUD de especialidades
│   ├── users/                 # Gestão de usuários
│   ├── reports/               # Relatórios
│   └── admin/                 # Painel administrativo
├── sql/
│   ├── database.sql           # Schema principal
│   ├── database_update.sql    # Atualizações de schema
│   ├── migration_v2.sql       # Migration v2 (visit_reason + exam_specialties)
│   ├── migration_patient_files.sql # Migration: arquivos de pacientes
│   ├── access_logs.sql        # Tabela de logs de acesso
│   ├── add_specialties.sql    # Tabela de especialidades
│   ├── sync_tokens.sql        # Tokens de sincronização
│   ├── seed_admin.sql         # Seed do admin padrão
│   ├── seed_test_data.sql     # Dados de teste
│   ├── clean_data.sql         # Limpeza de dados
│   └── clean_patients.sql     # Limpeza de pacientes
└── uploads/                   # Diretório de uploads (não versionado)
    └── .htaccess              # Proteção de acesso direto
```

---

## 🚀 Instalação

### Pré-requisitos
- PHP 8.0 ou superior
- MySQL 5.7+ ou MariaDB 10.3+
- Apache com `mod_rewrite` habilitado
- Extensões PHP: `pdo`, `pdo_mysql`, `mbstring`, `fileinfo`

### Passo a passo

1. **Clone o repositório**
   ```bash
   git clone https://github.com/ragazzon/medic.git
   cd medic
   ```

2. **Crie o banco de dados**
   ```sql
   CREATE DATABASE medic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Execute os scripts SQL na ordem**
   ```bash
   mysql -u root -p medic < sql/database.sql
   mysql -u root -p medic < sql/database_update.sql
   mysql -u root -p medic < sql/access_logs.sql
   mysql -u root -p medic < sql/add_specialties.sql
   mysql -u root -p medic < sql/sync_tokens.sql
   mysql -u root -p medic < sql/migration_v2.sql
   mysql -u root -p medic < sql/migration_patient_files.sql
   mysql -u root -p medic < sql/seed_admin.sql
   ```

4. **Configure a conexão com o banco**
   
   Edite `config/database.php` ou crie `config/database_local.php`:
   ```php
   <?php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'medic');
   define('DB_USER', 'seu_usuario');
   define('DB_PASS', 'sua_senha');
   ```

5. **Configure o Apache**
   
   Aponte o DocumentRoot para a pasta do projeto, ou coloque-o dentro de `htdocs`/`www`.

6. **Defina permissões de escrita**
   ```bash
   chmod -R 755 uploads/
   ```

7. **Acesse o sistema**
   
   Abra no navegador: `http://localhost/medic/`

### Login padrão (após seed_admin.sql)
- **Usuário:** `admin`
- **Senha:** `admin123` *(altere após o primeiro acesso)*

---

## 📦 Banco de Dados

### Tabelas principais

| Tabela | Descrição |
|---|---|
| `users` | Usuários do sistema (admin/comum) |
| `patients` | Pacientes/membros da família |
| `user_patients` | Vínculo usuário ↔ paciente (permissões) |
| `medical_records` | Prontuários / registros de consultas |
| `record_files` | Arquivos anexos aos prontuários |
| `exams` | Exames médicos |
| `exam_files` | Arquivos anexos aos exames |
| `exam_specialties` | Especialidades por exame (N:N) |
| `medications` | Medicamentos |
| `specialties` | Cadastro de especialidades médicas |
| `patient_files` | Arquivos extras genéricos por paciente |
| `access_logs` | Log de acessos ao sistema |

---

## 🔒 Segurança

- Senhas criptografadas com `password_hash()` (bcrypt)
- Proteção contra SQL Injection via PDO prepared statements
- Sanitização de output com `htmlspecialchars()`
- Controle de acesso por sessão e role (admin/user)
- Proteção da pasta `uploads/` via `.htaccess`
- Arquivo `config/database_local.php` excluído do versionamento

---

## 📝 Licença

Este projeto é de uso privado/familiar. Sinta-se à vontade para adaptar e usar conforme necessário.

---

## 👨‍💻 Autor

Desenvolvido para gerenciamento de saúde familiar.

Repositório: [github.com/ragazzon/medic](https://github.com/ragazzon/medic)