# Exemplo de roteiro: cadastro de barbearia

Este roteiro descreve como criar um cadastro de barbearias seguindo o estilo usado
no cadastro de notícias e de categorias deste projeto: PHP, POO, namespaces PSR-4,
PDO, helpers existentes e páginas administrativas. É um exemplo didático; confira
os arquivos atuais antes de implementar e adapte os campos às orientações da aula.

> Este documento não cria a funcionalidade. A tabela, o modelo, o serviço e as
> páginas abaixo ainda precisam ser implementados para que o cadastro funcione.
> Não altere o cadastro de notícias nem reutilize a tabela `noticias`.

## 1. Campos e tabela

Para este exemplo, cada barbearia tem nome, endereço, telefone e e-mail. A tabela
pode ser adicionada ao banco `microblog_2026` sem modificar as tabelas existentes:

```sql
CREATE TABLE barbearias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    telefone VARCHAR(30) NOT NULL,
    email VARCHAR(254) NOT NULL
);
```

Confirme os limites e a obrigatoriedade dos campos com o professor e mantenha-os
coerentes entre a tabela, o formulário, as validações e o modelo. Se for usar outro
banco, selecione-o na conexão e crie a tabela nele.

## 2. Arquivos seguindo a estrutura do projeto

| Arquivo | Responsabilidade |
| --- | --- |
| `src/Models/Barbearia.php` | Modelo tipado, construtor e getters |
| `src/Services/BarbeariaServico.php` | Consultas e inserção com PDO |
| `admin/barbearia-insere.php` | Formulário e processamento do cadastro |
| `admin/barbearias.php` | Listagem, caso faça parte da atividade |

O namespace do modelo e do serviço deve seguir o autoload já configurado:
`Microblog\Models` e `Microblog\Services`. Não é necessário criar outro autoload.

## 3. Modelo `Barbearia`

Crie uma classe `final` em `src/Models/Barbearia.php`, no namespace
`Microblog\Models`. Como em `Categoria` e `Noticia`, use propriedades privadas
tipadas e um construtor que recebe os dados. O ID pode ser `?int` e opcional no fim
do construtor, para permitir criar um objeto antes da inserção e também representar
um registro já existente.

Inclua getters para `id`, `nome`, `endereco`, `telefone` e `email`. O modelo não
deve abrir conexão nem executar SQL.

## 4. Serviço `BarbeariaServico`

Crie `src/Services/BarbeariaServico.php`, no namespace `Microblog\Services`, seguindo
`CategoriaServico`:

1. Declare `final class BarbeariaServico` e uma propriedade privada `PDO`.
2. No construtor, obtenha a conexão por `ConexaoBD::getConexao()`.
3. Implemente `inserir(Barbearia $barbearia): void`.
4. Use `INSERT INTO barbearias (nome, endereco, telefone, email)` com parâmetros
   nomeados; prepare a consulta e vincule os valores obtidos pelos getters.
5. Execute a consulta dentro de `try`. Em caso de `PDOException`, registre com
   `Utils::registrarLog($e)` e lance uma exceção com mensagem genérica.

Se a atividade pedir listagem, busca, atualização ou exclusão, implemente métodos
separados usando prepared statements e o padrão já visto em `CategoriaServico`.
Não concatene dados do formulário diretamente no SQL.

## 5. Validação dos dados

No processamento do formulário, normalize os valores com
`Utils::normalizarEntrada()`. Use `Validacoes::validarNome()` para o nome e
`Validacoes::validarEmail()` para o e-mail. Endereço e telefone também precisam ser
validados como obrigatórios; caso as regras necessárias não existam em `Validacoes`,
combine com o professor se devem ser criados métodos específicos antes de usá-los.

Valide os dados antes de criar o modelo ou chamar o serviço. Não use escaping HTML
antes de salvar; escape os valores somente ao exibi-los, com
`Utils::escaparHtml()`.

## 6. Página `admin/barbearia-insere.php`

Siga o fluxo de `admin/categoria-insere.php` para autorização, formulário e retorno
de erros, e a organização de `admin/noticia-insere.php` para processamento de POST:

1. Carregue `vendor/autoload.php` e proteja a página com o controle de acesso
   apropriado. Para uma página administrativa restrita a administradores, use
   `ControleDeAcesso::exigirAdmin()`.
2. Inicialize `$mensagemErro` e uma variável para cada campo, como string vazia.
3. Quando `$_SERVER['REQUEST_METHOD'] === 'POST'`, normalize os valores enviados,
   valide-os e crie `new Barbearia(...)`.
4. Crie `BarbeariaServico`, chame `inserir($barbearia)` e, após sucesso, redirecione
   com `header('Location: barbearias.php')` seguido de `exit`.
5. Trate `InvalidArgumentException` como erro de validação, exibindo sua mensagem.
   Para outras falhas, registre a exceção com `Utils::registrarLog($e)` e mostre uma
   mensagem genérica, sem detalhes internos.
6. Inclua `cabecalho-admin.php` e `rodape-admin.php`. O formulário usa `method="post"`
   e um campo para cada dado. Não precisa de `enctype="multipart/form-data"` porque
   este exemplo não envia arquivo.
7. Ao reapresentar o formulário após erro, preencha os campos com os valores
   normalizados e escapados com `Utils::escaparHtml()`.

## 7. Verificação

Depois de implementar os arquivos PHP, na raiz do projeto:

```sh
composer dump-autoload
php -l src/Models/Barbearia.php
php -l src/Services/BarbeariaServico.php
php -l admin/barbearia-insere.php
```

`composer dump-autoload` segue o procedimento do projeto; como o mapeamento
`Microblog\\` para `src/` já existe, confira o resultado e não altere `composer.json`
sem necessidade. Em seguida, teste no servidor local: acesso permitido, envio válido,
campos inválidos, persistência no banco e mensagens de erro sem exposição de detalhes.

## Prompts para enviar ao Copilot

Envie os prompts abaixo na ordem, mantendo a mesma conversa do Copilot para que ele
considere as decisões anteriores. O primeiro é apenas para análise; nos seguintes,
peça alterações somente na etapa indicada. Os campos deste exemplo são `nome`,
`endereco`, `telefone` e `email`.

### Prompt 1 — analisar o padrão antes de editar

```text
Quero criar uma página administrativa para cadastrar barbearias neste projeto,
seguindo o estilo didático usado no cadastro de notícias e categorias. Antes de
editar qualquer arquivo, leia AGENTS.md, admin/noticia-insere.php,
admin/categoria-insere.php, src/Models/Noticia.php, src/Models/Categoria.php,
src/Services/NoticiasServico.php, src/Services/CategoriaServico.php,
src/Helpers/Validacoes.php e o SQL atual do banco.

Resuma o fluxo que devo seguir, os arquivos novos necessários e qualquer
incompatibilidade ou decisão que precisa ser confirmada. Considere os campos nome,
endereco, telefone e email. Não altere arquivos nesta etapa, não modifique o
cadastro de notícias e não invente regras ou permissões diferentes das existentes.
```

### Prompt 2 — preparar a tabela

```text
Com base na análise anterior, prepare o SQL para a tabela barbearias, com id
auto_increment e os campos nome, endereco, telefone e email obrigatórios. Siga os
tipos e limites definidos no roteiro resumoexemplar.md e confira se não há conflito
com o esquema existente. Salve a alteração em um script SQL separado dentro de
_materiais/, sem substituir nem reescrever os dumps atuais. Mostre o SQL e explique
como executá-lo localmente; não tente conectar ao banco nem executar comandos
destrutivos.
```

### Prompt 3 — criar modelo e serviço

```text
Implemente somente src/Models/Barbearia.php e
src/Services/BarbeariaServico.php seguindo o padrão de Categoria e
CategoriaServico. O modelo deve ser final, usar propriedades privadas tipadas, ter
construtor e getters para id, nome, endereco, telefone e email; o id deve ser
opcional para uma nova barbearia. O serviço deve obter PDO por
ConexaoBD::getConexao() e implementar inserir(Barbearia $barbearia): void com
prepared statement e parâmetros vinculados. Registre PDOException com
Utils::registrarLog() e lance uma mensagem genérica. Não altere outras classes nem
crie CRUD adicional. Ao terminar, mostre os arquivos alterados.
```

### Prompt 4 — validações e formulário de cadastro

```text
Implemente a página admin/barbearia-insere.php seguindo a organização de
admin/categoria-insere.php e admin/noticia-insere.php, usando o modelo e serviço
Barbearia criados nesta conversa. Inclua autoload, proteção de acesso no mesmo
padrão administrativo já usado pelo projeto, formulário POST e campos nome,
endereco, telefone e email. Normalize as entradas com Utils, valide antes de criar
o modelo, use Validacoes::validarNome() e validarEmail() onde forem adequados e
adicione apenas as validações específicas indispensáveis para endereço e telefone,
sem enfraquecer as regras existentes.

Trate InvalidArgumentException como erro de validação; registre falhas inesperadas
com Utils::registrarLog() e apresente uma mensagem segura. Escape os valores
reapresentados no formulário com Utils::escaparHtml(). Após inserir, redirecione
para admin/barbearias.php e use exit. Inclua cabeçalho e rodapé administrativos.
Não implemente upload de imagem e não altere o cadastro de notícias.
```

### Prompt 5 — criar listagem para o redirecionamento

```text
Crie admin/barbearias.php para completar o fluxo do formulário de cadastro.
Siga o padrão de listagem de categorias/notícias: controle de acesso coerente com
as demais páginas administrativas, cabeçalho e rodapé compartilhados, tratamento
seguro de erros e saída escapada com Utils::escaparHtml(). Acrescente ao
BarbeariaServico somente o método de listagem necessário, usando PDO. Mostre nome,
endereco, telefone e email numa tabela. Não implemente atualização ou exclusão e
não altere outras áreas do sistema.
```

### Prompt 6 — revisar e verificar

```text
Revise somente as alterações do cadastro de barbearias feitas nesta conversa.
Confira se a tabela SQL, os campos do modelo, os parâmetros do INSERT, as validações
e os nomes do formulário estão coerentes; confira também namespaces PSR-4,
prepared statements, escaping, logs e autorização. Corrija apenas problemas
relacionados a este cadastro. Execute composer dump-autoload e php -l nos arquivos
PHP criados ou alterados. Não altere arquivos não relacionados; ao final, liste
arquivos modificados e os resultados dos comandos.
```
