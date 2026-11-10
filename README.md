# Microblog

Projeto didático de um portal de notícias desenvolvido para praticar programação **back-end em PHP** com **Programação Orientada a Objetos (POO)** e banco de dados **MySQL/MariaDB (via PDO)**.

---

## Visão geral do projeto

O **Microblog** é dividido em duas áreas principais:

### 1. Área pública (raiz do projeto)
Voltada aos leitores do portal:
- **Página inicial (`index.php`):** exibe notícias em destaque e a listagem geral de notícias.
- **Notícia completa (`noticia.php`):** apresenta o texto completo, a data de publicação, o autor, a categoria e a imagem da notícia.
- **Notícias por categoria (`noticias-por-categoria.php`):** filtra as publicações de acordo com a categoria escolhida no menu.
- **Busca de notícias (`resultados.php` e `js/busca.js`):** pesquisa notícias por termo com atualização dinâmica no navegador.
- **Acesso (`login.php`):** formulário de autenticação para entrar no painel administrativo.

### 2. Área administrativa (`admin/`)
Reúne as telas de gerenciamento de notícias, categorias, usuários e perfil. O projeto também define os tipos de usuário **editor** e **administrador**; o controle completo de permissões por tipo ainda está em desenvolvimento.

---

## Funcionalidades implementadas

- Páginas públicas para listar notícias, consultar uma notícia, filtrar por categoria e pesquisar.
- Painel administrativo com operações de cadastro, consulta, atualização e exclusão para categorias e usuários, além das telas de gerenciamento de notícias.
- Acesso ao banco de dados MySQL/MariaDB por PDO, com modelos e serviços organizados em `src/`.
- Autoload PSR-4 configurado pelo Composer, validações e utilitários para normalizar entradas, escapar conteúdo HTML e registrar erros.

## Atualizações recentes — 10/11/2026

- Corrigidos os IDs usados nos links de atualização e exclusão de categorias.
- Implementadas as exclusões de categorias e usuários com consultas preparadas, validação do ID recebido e registro de erros.
- Criado o controle de acesso por sessão. O cabeçalho administrativo exige que exista uma sessão autenticada antes de exibir o painel.
- Atualizado o formulário de acesso para validar o preenchimento de e-mail e senha e exibir mensagens para campos obrigatórios, credenciais inválidas, acesso proibido, erros e saída do sistema.

### Estado da autenticação

O controle de sessão já oferece métodos para registrar e encerrar a sessão. A autenticação ainda não está integrada por completo: o formulário de login valida campos vazios, mas ainda não verifica as credenciais no banco; a tela de saída também permanece como etapa em desenvolvimento. O bloqueio atual do painel verifica se há uma sessão iniciada, sem aplicar ainda permissões específicas por tipo de usuário.

---

## Estrutura de pastas

```text
├── admin/              # Páginas e scripts exclusivos do painel administrativo
│   └── js/             # Scripts de confirmação de exclusão e contador de caracteres
├── css/                # Folha de estilos personalizada
├── images/             # Imagens das notícias e ícones do site
├── includes/           # Cabeçalhos, rodapés e blocos compartilhados entre páginas
├── js/                 # Script da busca dinâmica na área pública
├── logs/               # Registro de erros da aplicação (erros.log)
├── src/                # Código-fonte PHP orientado a objetos
│   ├── Auth/           # Controle de sessão e permissões (desenvolvido em aula)
│   ├── Database/       # Conexão com o banco de dados via PDO (ConexaoBD)
│   ├── Enums/          # Enumerações (Destaque, TipoUsuario)
│   ├── Helpers/        # Funções auxiliares, upload e validações (Utils, Validacoes)
│   ├── Models/         # Modelos de dados (Categoria, Noticia, Usuario)
│   └── Services/       # Operações de banco de dados / CRUD (desenvolvidas em aula)
└── composer.json       # Configuração do autoload PSR-4
```

---

## Requisitos e configuração do ambiente

### 1. Servidor e PHP
- **PHP 8.5 ou superior** com as extensões `pdo_mysql`, `mbstring` e `fileinfo` habilitadas no `php.ini`.
- Servidor web local (PHP Server, ou Apache via XAMPP, ou WampServer ou similar) e **MySQL / MariaDB**.

### 2. Banco de dados
1. Acesse o **phpMyAdmin** (ou outro cliente MySQL).
2. Importe o arquivo SQL da base de dados fornecido pelo professor para criar o banco `microblog_2026` e suas tabelas (`usuarios`, `categorias` e `noticias`).
3. Confira as credenciais de acesso (`$servidor`, `$usuario`, `$senha` e `$banco`) em `src/Database/ConexaoBD.php`.

### 3. Autoload de classes com Composer
Com o [Composer](https://getcomposer.org/) instalado no computador e o arquivo `composer.json` configurado com o mapeamento PSR-4 (`"Microblog\\": "src/"`), execute no terminal, na pasta raiz do projeto:

```bash
composer dump-autoload
```

Isso gerará a pasta `vendor/` e o arquivo `vendor/autoload.php`, responsável por carregar automaticamente as classes do projeto.
