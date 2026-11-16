# Guia de desenvolvimento do Microblog

Este arquivo registra o estado observado do repositório e as convenções a seguir ao
trabalhar nele. O projeto é didático; confira o código atual antes de presumir que uma
funcionalidade descrita em material antigo já esteja implementada.

## Visão geral e estado atual

Microblog é um portal de notícias fictício feito em PHP, com páginas públicas e um
painel administrativo, usando MySQL/MariaDB por PDO. A interface e os comentários do
código são principalmente em português.

- A área pública fica na raiz. `index.php`, `noticia.php`,
  `noticias-por-categoria.php`, `resultados.php` e `includes/todas.php` ainda exibem
  conteúdo provisório/estático; não presumir que a leitura pública de notícias ou a
  busca estejam integradas ao banco.
- O login é implementado em `login.php`. O painel administrativo está em `admin/`.
- A gestão de usuários e categorias possui fluxos administrativos de listagem,
  inclusão, atualização e exclusão. O perfil do usuário autenticado é editado em
  `admin/meu-perfil.php`.
- O fluxo administrativo de notícias lista e insere notícias. `admin/noticia-atualiza.php`
  e `admin/noticia-exclui.php` ainda são páginas provisórias, e
  `NoticiasServico` ainda não implementa atualização ou exclusão.
- Há diferenças entre o README/material didático e o código: as telas administrativas
  de categorias e notícias chamam `exigirAdmin()`, portanto, atualmente, editores não
  podem acessá-las por essas páginas. Não tratar a descrição antiga de permissões como
  comportamento confirmado.

## Estrutura do repositório

- `src/Auth/`: autenticação por sessão e autorização (`ControleDeAcesso`).
- `src/Database/`: conexão PDO (`ConexaoBD`).
- `src/Enums/`: tipos de usuário e destaque.
- `src/Helpers/`: normalização, escaping, logs, upload, senha, data e validações.
- `src/Models/`: modelos simples de usuário, categoria e notícia.
- `src/Services/`: consultas e operações de banco por entidade.
- `admin/`: páginas de administração e scripts locais de confirmação/contador.
- `includes/`: cabeçalhos, rodapés e blocos compartilhados.
- `css/`, `js/`, `images/`: estilos, busca pública e imagens.
- `_materiais/`: dumps SQL e material de apoio didático.
- `logs/erros.log`: destino configurado para logs de erro.

## Ambiente e configuração

O `README.md` documenta PHP 8.5 ou superior, extensões `pdo_mysql`, `mbstring` e
`fileinfo`, servidor web local (por exemplo, Apache/XAMPP) e MySQL/MariaDB. O código
usa recursos tipados e enums do PHP.

1. Crie/selecione o banco `microblog_2026` e importe
   `_materiais/microblog_2026_completo.sql` para obter o esquema e os dados de exemplo.
2. Configure servidor, banco, usuário e senha em `src/Database/ConexaoBD.php` para o
   ambiente local. Não grave credenciais reais no repositório.
3. O Composer mapeia `Microblog\\` para `src/` via PSR-4. Para regenerar o autoload:

   ```sh
   composer dump-autoload
   ```

   Os arquivos de aplicação carregam `vendor/autoload.php`. `composer.lock` não lista
   dependências PHP externas.
4. Bootstrap e Bootstrap Icons são carregados por CDN nos cabeçalhos compartilhados.

Não há configuração de suíte de testes ou script de lint definido no Composer. Para
validar sintaxe de um arquivo PHP alterado, use:

```sh
php -l caminho/do/arquivo.php
```

## Arquitetura e convenções

- Mantenha o mapeamento PSR-4: diretórios e namespaces correspondem sob `src/`, por
  exemplo, `src/Services/NoticiasServico.php` declara `Microblog\Services`.
- Classes e enums usam PascalCase; nomes de arquivos seguem o nome da classe. Métodos,
  propriedades e variáveis usam camelCase. Os identificadores de domínio e mensagens
  existentes são em português.
- Os modelos são classes finais com propriedades tipadas privadas, construtores e
  getters; não concentram acesso ao banco nem regras de apresentação.
- Os serviços são classes finais, obtêm PDO no construtor e recebem modelos tipados
  para inserções/atualizações. Consultas com entrada usam prepared statements e
  parâmetros vinculados. O modo padrão de fetch é `PDO::FETCH_ASSOC`.
- Use `TipoUsuario` (`admin`, `editor`) e `Destaque` (`sim`, `nao`) em vez de
  duplicar valores de domínio; ao persistir/ler formulários, converta com `->value`,
  `::tryFrom()` ou `::from()` conforme o fluxo existente.
- As telas normalmente fazem o processamento PHP no início do arquivo, incluem o
  cabeçalho/rodapé compartilhado e mantêm os nomes dos arquivos administrativos no
  padrão `entidade-acao.php` (por exemplo, `categoria-insere.php`).
- Preserve os helpers e o estilo procedural das páginas existentes; evite introduzir
  dependências ou abstrações sem necessidade.

## Modelos, serviços e banco

- `Usuario`: `id`, `nome`, `email`, `senha` (hash) e `tipo`.
- `Categoria`: `id` e `nome`.
- `Noticia`: `id`, `data`, `titulo`, `texto`, `resumo`, `imagem`, `destaque`,
  `usuarioId` e `categoriaId`. A ordem dos parâmetros do construtor deve ser conferida
  em `src/Models/Noticia.php` antes de instanciar.
- `UsuarioServico`: `listarTodos`, `inserir`, `buscarPorId`, `atualizar`, `excluir` e
  `buscarPorEmail`.
- `CategoriaServico`: `listarTodos`, `inserir`, `buscarPorId`, `atualizar` e `excluir`.
- O nome correto da classe/arquivo do serviço de notícias é `NoticiasServico`
  (plural): `listarTodos` e `inserir`. `listarTodos($usuarioId, $tipoUsuario)` retorna
  todas as notícias para `admin` e filtra pelo autor para os demais tipos.
- As tabelas principais são `usuarios`, `categorias` e `noticias`. A tabela de
  notícias relaciona autor e categoria por `usuario_id` e `categoria_id`; no dump
  completo essas FKs permitem `NULL` e usam `ON DELETE SET NULL`.
- Use `_materiais/microblog_2026_completo.sql` como referência do banco atual. O texto
  `_materiais/modelagem-fisica.md` é material antigo: menciona `microblog_2025` e
  limites diferentes. Há também diferenças entre banco e aplicação (por exemplo,
  `resumo` comporta até 500 caracteres no SQL atual, enquanto a validação e os
  formulários limitam a 300).

## Entrada, validação, segurança e erros

- Normalize dados de formulário/URL com `Utils::normalizarEntrada()`. O modo padrão
  (texto) e o modo `email` exigem string e aplicam `trim`; o modo `inteiro` valida
  inteiro e retorna `0` para valor inválido. A normalização não substitui validação
  nem escaping de saída.
- Use `Validacoes` para as regras já existentes: nome/título/texto não vazios após
  `trim`; e-mail válido; senha não vazia e com pelo menos 5 caracteres; tipo e
  destaque pertencentes aos enums; resumo não vazio e com até 300 caracteres;
  categoria com ID positivo.
- Escape dados dinâmicos ao renderizar HTML com `Utils::escaparHtml()`. O helper
  usa `htmlspecialchars` com `ENT_QUOTES`, `ENT_SUBSTITUTE` e UTF-8. Não faça escaping
  antes de persistir texto; preserve o texto e escape ao exibir.
- Senhas novas devem ser armazenadas com `Utils::codificarSenha()`/`password_hash`;
  autenticação compara com `password_verify`. Na edição, senha vazia mantém o hash
  atual e senha preenchida é validada e comparada com `Utils::verificarSenha()`.
- Rotas privadas devem usar `ControleDeAcesso::exigirLogin()` ou
  `ControleDeAcesso::exigirAdmin()` de acordo com a permissão necessária. A sessão
  guarda `id`, `nome` e `tipo`; os valores de perfil vêm de `TipoUsuario`.
- Para IDs de URL, siga o padrão de normalizar e chamar `Utils::verificarId()`. A
  função redireciona para `index.php` se o ID não for inteiro positivo.
- Upload de imagem é feito por `Utils::upload()`: valida erro de upload, arquivo
  enviado, tamanho máximo de 2 MB, MIME e extensão coerentes (JPEG, GIF, PNG ou WebP)
  e nome de até 60 caracteres; salva em `images/`. Formulários com upload precisam de
  `enctype="multipart/form-data"`.
- Serviços registram `PDOException` com `Utils::registrarLog()` e lançam uma mensagem
  genérica. Nas páginas, trate `InvalidArgumentException` como erro de validação e
  `Throwable` como falha inesperada: registre a exceção e mostre uma mensagem segura,
  sem expor detalhes internos.
- Ao exibir erros e preencher novamente formulários, escape os valores. Após sucesso,
  mantenha o padrão atual de redirecionar com `header()` e encerrar com `exit`.

## Observações para futuras alterações

- Leia a implementação atual antes de seguir descrições do `README.md`,
  `resumo.md` ou dos materiais de aula; esses documentos podem descrever etapas
  planejadas ou versões anteriores.
- Ao implementar o restante do CRUD de notícias, mantenha coerentes modelo, serviço,
  formulários, autorização e esquema. Considere que o ID de autor pode ser nulo no
  banco, embora o construtor atual de `Noticia` exija `int`.
- Não altere regras de acesso ou limites de validação implicitamente: documente e
  teste mudanças comportamentais, especialmente a discrepância entre o resumo de 300
  caracteres na aplicação e a coluna SQL de 500.
- As telas de confirmação de exclusão usam um `<dialog>` compartilhado no cabeçalho
  administrativo e `admin/js/confirm.js`. O contador do resumo está em
  `admin/js/contador.js` e pressupõe os elementos `#maximo` e `#resumo`.
