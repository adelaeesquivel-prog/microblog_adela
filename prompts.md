# Prompts e decisões do desenvolvimento do Microblog

## Histórico administrativo anterior preservado

### Prompt anterior 1

veja a pasta noticiasServico.php e faça o codigo no noticia-atualiza.php. faça com base nos módulos anteriores, declarando assim os comdandos e deixando atualizar as noticias .

### Prompt anterior 2

so o tipo admin pode atualizar e ver as noticias de tds os usuarios , o tipo editor pode ver so as noticias q ele cadastrou,so pode mudar as noticias q ele mesmo cadastrou.

### Prompt anterior 3

Analise todas as classes utilizadas até o momento (contidas na pasta src), em especial a classe NoticiaServico e a classe Noticia.

Veja também as páginas administrativas, em especial usuários php e categorias,php.

Identifique padrões, como nomes de métodos. propriedades, variáveis, arquivos, namespaces

com esses métodos programe a NoticiaServico a função programar a página noticia-exclui. que permita excluir as noticias cadastradas

### Prompt anterior 4

Guarde o prompt no arquivo prompts.md (crie, caso não tenha)
guarde os meus 4 ultimos prompts nesse arquivo

## Prompts das páginas públicas e serviços

### 1. `includes/cabecalho.php` — Menu dinâmico de categorias

Analise o projeto Microblog e programe `includes/cabecalho.php` para exibir dinamicamente todas as categorias cadastradas no banco.

Requisitos:

- Utilize a classe existente `CategoriaServico` e seu método de listagem.
- Siga o padrão de conexão PDO e a organização das outras classes.
- Carregue o autoload do Composer pelo caminho correto e instancie `CategoriaServico`.
- Armazene o resultado de `listarTodos()` em `$listaDeCategorias`.
- Trate possíveis erros com try/catch.
- Utilize `foreach` para percorrer as categorias.
- Gere para cada categoria um link `noticias-por-categoria.php?id=` com o ID correspondente.
- Remova as categorias escritas manualmente no HTML.
- Preserve a estrutura HTML, o Bootstrap e o visual atual.
- Não invente métodos, atributos ou classes; analise os arquivos existentes primeiro.
- Use `htmlspecialchars()` para exibir o nome com segurança e explique as alterações.

### 2. `index.php` — Exibição das notícias em destaque

Analise `NoticiasServico.php`, o modelo `Noticia.php` e o enum `Destaque.php`. Identifique o método existente para obter notícias em destaque e use dados do banco.

Requisitos:

- Exiba a imagem, o título e o resumo de cada notícia em destaque.
- Gere o link dinâmico `noticia.php?id=` usando o ID real da notícia.
- Não use notícias estáticas nem IDs fixos.
- Mantenha a consulta `NoticiasServico->listarTodos(0, TipoUsuario::ADMIN->value)`.
- Trate erros com `try/catch (Throwable $e)`, `Utils::registrarLog($e)` e uma variável de mensagem.
- Determine destaques com `foreach` e `if`, verificando `$noticia['destaque'] === Destaque::SIM->value`.
- Não use `array_filter()`, `fn()`, funções anônimas ou recursos não ensinados.
- Use `Utils::escaparHtml()` e siga o padrão de `admin/categorias.php`.
- Preserve o layout Bootstrap e as inclusões de cabeçalho, `includes/todas.php` e rodapé.
- Não altere `Utils.php`, `NoticiasServico.php`, enums, models nem outros arquivos.
- Explique as alterações.

### 3. `includes/todas.php` — Listagem de todas as notícias

Use `includes/todas.php` para exibir todas as notícias cadastradas, usando dados reais do banco e preservando o layout Bootstrap.

Requisitos:

- Mantenha `if (!isset($listaDeNoticias))`, pois o arquivo também é incluído por `index.php`.
- Use `NoticiasServico->listarTodos(0, TipoUsuario::ADMIN->value)` quando a lista ainda não estiver definida.
- Trate erros com `try/catch (Throwable $e)`, `Utils::registrarLog($e)` e `$erroAoCarregarNoticias`.
- Mantenha a condição para lista vazia e o `foreach`.
- Exiba data em `dd/mm/aaaa`, título e resumo.
- Use `Utils::escaparHtml()` nos dados e mantenha links dinâmicos para `noticia.php?id=...`.
- Preserve o HTML e o Bootstrap.
- Não invente métodos, não use funções ou sintaxes não ensinadas, não altere outros arquivos nem mude a lógica.
- Explique as alterações.

### 4. `noticia.php` — Exibição completa de uma notícia

Substitua o conteúdo provisório da página pública `noticia.php` por dados do banco.

Requisitos:

- Receba o ID por `$_GET['id']`.
- Antes de criar validações, confira como `Utils` e `Validacoes` são usados; valide o ID com os recursos existentes.
- Use `NoticiasServico::buscarPorId()` sem criar métodos.
- Como a página é pública, passe usuário `0` e `TipoUsuario::ADMIN->value`.
- Exiba título, data no formato `dd/mm/aaaa`, autor, imagem e texto completo.
- Se o autor for nulo ou vazio, mostre “Equipe Microblog”.
- Para ID inválido ou notícia inexistente, apresente mensagem amigável.
- Proteja os dados exibidos com `Utils::escaparHtml()` ou o padrão já adotado.
- Preserve `includes/cabecalho.php`, `includes/todas.php`, `includes/rodape.php` e o layout Bootstrap.
- Não altere outras páginas, não invente métodos e explique as modificações.

### 5. `noticias-por-categoria.php` — Notícias filtradas por categoria

Use o ID da categoria recebido na URL para localizar a categoria e exibir suas notícias.

Requisitos da abordagem direta com PDO:

- Normalize `$_GET['id']` com `Utils::normalizarEntrada(..., 'inteiro')` e valide com `Utils::verificarId()`.
- Use `CategoriaServico::buscarPorId()` para obter o nome da categoria; trate categoria inexistente.
- Obtenha a conexão com `ConexaoBD::getConexao()` e faça a consulta SQL diretamente nesta página.
- Não dependa de métodos novos em `NoticiasServico.php`.
- Consulte `noticias` filtrando por `categoria_id`, com `LEFT JOIN usuarios` para obter o autor.
- Selecione ID, data, título, resumo e autor; ordene pela data mais recente primeiro.
- Use `prepare()`, `bindValue()`, `execute()` e `fetchAll()`.
- Trate erros com `try/catch (Throwable $e)` e `Utils::registrarLog($e)`.
- Exiba título, data `dd/mm/aaaa`, autor ou “Equipe Microblog”, resumo e link dinâmico `noticia.php?id=...`.
- Trate ID não informado, categoria inexistente e categoria sem notícias.
- Preserve o HTML, Bootstrap e as inclusões de cabeçalho, `includes/todas.php` e rodapé.
- Não altere banco nem outros arquivos; explique as alterações.

Especificação posterior sobre o nome da categoria:

- No banco `microblog_2026`, os registros informados eram: ID 1 Segurança; ID 2 Mercado; ID 4 Mobile; ID 5 Games; ID 6 Educação; ID 7 Desenvolvimento.
- Ao acessar `noticias-por-categoria.php?id=7`, o nome exibido era Segurança.
- Identifique a causa e corrija somente o problema, sem modificar `Utils`, `CategoriaServico` funcional ou a conexão.

### 6. `NoticiasServico.php` — Tentativa de padronização do serviço (não utilizada)

Foi considerada a atualização de `NoticiasServico.php` para seguir a organização e simplicidade de `CategoriaServico.php`.

Especificações consideradas:

- Manter namespace, imports, classe, conexão PDO e construtor com `ConexaoBD::getConexao()`.
- Preservar todas as funções, parâmetros, verificações, regras de acesso e funcionalidades de `listarTodos()`, `buscarPorId()`, `inserir()`, `atualizar()` e `excluir()`.
- Usar `try/catch (PDOException $e)`, `Utils::registrarLog($e)` e exceções com mensagens claras.
- Manter o funcionamento das páginas administrativas.
- Não alterar outros arquivos nem o banco; não inventar tabelas ou colunas.
- Evitar `array_filter`, funções anônimas, `fn`, recursos avançados e sintaxes não ensinadas.

**Status:** tentativa considerada, mas não utilizada na solução documentada para as páginas públicas.

### 7. `NoticiasServico.php` — Tentativa de acrescentar busca por categoria (não utilizada)

Foi considerada a criação de `listarPorCategoria()` em `NoticiasServico.php`.

Especificações consideradas:

- Receber o ID da categoria como inteiro.
- Consultar `noticias` e selecionar `id`, `data`, `titulo`, `resumo`, `texto`, `imagem` e `u.nome AS autor`.
- Fazer `LEFT JOIN usuarios u ON u.id = n.usuario_id`.
- Filtrar com `WHERE n.categoria_id = :categoria_id` e ordenar com `ORDER BY n.data DESC`.
- Usar `$this->conexao->prepare($sql)`, `bindValue()` com `PDO::PARAM_INT`, `execute()` e `fetchAll()`.
- Tratar `PDOException` com `Utils::registrarLog($e)` e lançar `Exception` com mensagem clara.
- Acrescentar somente o método, sem alterar os métodos existentes ou outros arquivos.

**Status:** tentativa considerada, mas não utilizada; a busca por categoria fica diretamente em `noticias-por-categoria.php`.

## Outras solicitações e esclarecimentos preservados

- Foi solicitado que `index.php` também exibisse todas as notícias através de `includes/todas.php`, com dados reais.
- Foi esclarecido que admin pode acessar e atualizar todas as notícias, enquanto editor pode acessar e atualizar somente as próprias.
- Houve uma solicitação posterior para a página de categoria usar `CategoriaServico` e `NoticiaServico`, exibir título/data/autor/resumo, usar “Equipe Microblog” quando não houver autor e registrar o prompt sem apagar os anteriores. A decisão documentada neste arquivo, porém, é a consulta direta via PDO descrita no item 5.
- Também foram enviados pedidos para resumir os prompts e, em seguida, para manter as especificações completas em ordem cronológica. A orientação atual é registrar as especificações, não resumi-las.

## Decisões e observações do projeto

- Seguir o padrão ensinado pelo professor, especialmente `admin/categorias.php` e `CategoriaServico.php`.
- Usar estruturas simples ensinadas em aula; não introduzir recursos avançados ou sintaxes não estudadas.
- Preservar os arquivos originais do professor e o funcionamento das páginas existentes.
- Para a solução documentada da página por categoria, foi decidido não modificar `NoticiasServico.php`.
- A consulta de notícias por categoria deve ser feita diretamente em `noticias-por-categoria.php`, usando `ConexaoBD::getConexao()` e PDO.
- Os prompts dos itens 6 e 7 são tentativas consideradas e não utilizadas na solução documentada.

## Comandos diretos para as páginas públicas

### 1. `includes/cabecalho.php` — Menu dinâmico de categorias

Veja `includes/cabecalho.php` e `CategoriaServico.php`. Faça o menu carregar as categorias usando o autoload do Composer e o método existente `listarTodos()`. Percorra `$listaDeCategorias` com `foreach` e crie um link `noticias-por-categoria.php?id=` para cada ID. Remova os itens escritos manualmente. Escape os nomes com `htmlspecialchars()`, trate erros e preserve o HTML, o Bootstrap e o visual atual. Não invente métodos nem altere outros arquivos.

### 2. `index.php` — Notícias em destaque

Veja `index.php`, `NoticiasServico.php`, `Noticia.php`, `Destaque.php` e `admin/categorias.php`. Faça a página carregar as notícias com `NoticiasServico->listarTodos(0, TipoUsuario::ADMIN->value)`. Trate erros com `try/catch (Throwable $e)`, registre-os com `Utils::registrarLog($e)` e controle a mensagem de erro em uma variável. Use `foreach` e `if` para exibir somente notícias cujo destaque seja `Destaque::SIM->value`. Mostre imagem, título e resumo, com link dinâmico para `noticia.php?id=...`. Escape os valores com `Utils::escaparHtml()`. Não use `array_filter()`, `fn()` ou recursos não ensinados; preserve Bootstrap e as inclusões existentes.

### 3. `includes/todas.php` — Todas as notícias

Veja `includes/todas.php`, `index.php`, `NoticiasServico.php` e `admin/categorias.php`. Mantenha `if (!isset($listaDeNoticias))` para buscar as notícias com o método existente `listarTodos(0, TipoUsuario::ADMIN->value)` quando a lista não tiver sido carregada. Trate erros com `try/catch (Throwable $e)`, `Utils::registrarLog($e)` e `$erroAoCarregarNoticias`. Mostre uma mensagem se não houver notícias; caso existam, percorra-as com `foreach`. Exiba data em `dd/mm/aaaa`, título e resumo escapados com `Utils::escaparHtml()`, e links dinâmicos para `noticia.php?id=...`. Preserve o HTML e o Bootstrap.

### 4. `noticia.php` — Detalhes de uma notícia

Veja `noticia.php`, `NoticiasServico.php`, `Noticia.php`, `Utils.php` e `Validacoes.php`. Receba e valide o ID da URL com os recursos existentes. Busque a notícia com `NoticiasServico::buscarPorId(0, TipoUsuario::ADMIN->value, $noticiaId)`. Exiba título, data em `dd/mm/aaaa`, autor, imagem e texto completo. Se o autor estiver vazio ou nulo, mostre “Equipe Microblog”. Se o ID for inválido ou a notícia não existir, mostre uma mensagem amigável. Escape os dados e mantenha cabeçalho, `includes/todas.php`, rodapé e Bootstrap. Não crie métodos nem altere outras páginas.

### 5. `noticias-por-categoria.php` — Notícias da categoria selecionada

Veja `noticias-por-categoria.php`, `CategoriaServico.php`, `ConexaoBD.php` e os exemplos de PDO do projeto. Receba o ID por GET, normalize-o com `Utils::normalizarEntrada(..., 'inteiro')` e valide-o com `Utils::verificarId()`. Localize a categoria com `CategoriaServico::buscarPorId()` e mostre seu nome. Se o ID estiver ausente ou a categoria não existir, mostre uma mensagem adequada. Para as notícias, obtenha PDO com `ConexaoBD::getConexao()` e faça nesta página uma consulta preparada à tabela `noticias`, com `LEFT JOIN usuarios`, filtro pelo `categoria_id` e ordenação da mais recente para a mais antiga. Use `prepare()`, `bindValue()`, `execute()` e `fetchAll()`. Trate erros com `try/catch (Throwable $e)` e `Utils::registrarLog($e)`. Exiba título, data em `dd/mm/aaaa`, autor ou “Equipe Microblog”, resumo e link dinâmico `noticia.php?id=...`. Se não houver notícias, mostre uma mensagem. Preserve cabeçalho, `includes/todas.php`, rodapé e Bootstrap.



### 6. `NoticiasServico.php` — Busca por categoria

Veja `NoticiasServico.php` e `CategoriaServico.php`. Acrescente um método `listarPorCategoria(int $categoriaId)` que consulte notícias pela categoria, selecione ID, data, título, resumo, texto, imagem e nome do autor, use `LEFT JOIN usuarios`, `prepare()`, `bindValue()` com `PDO::PARAM_INT`, `execute()`, `fetchAll()` e ordene por data decrescente. Registre erros de PDO e lance uma `Exception` com mensagem clara. Não altere os outros métodos nem outros arquivos.


