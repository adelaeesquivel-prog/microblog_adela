<?php
use Microblog\Helpers\Utils;
use Microblog\Services\CategoriaServico;
use Microblog\Services\NoticiasServico;

require_once __DIR__ . '/vendor/autoload.php';

$categoriaId = Utils::normalizarEntrada($_GET['id'] ?? null, 'inteiro');
$categoriaSelecionada = null;
$listaNoticiasCategoria = [];
$mensagemErro = "";

if ($categoriaId <= 0) {
    $mensagemErro = "Informe uma categoria válida.";
} else {
    try {
        $categoriaServico = new CategoriaServico();
        $categoriaSelecionada = $categoriaServico->buscarPorId($categoriaId);

        if ($categoriaSelecionada === null) {
            $mensagemErro = "A categoria solicitada não foi encontrada.";
        } else {
            $noticiasServico = new NoticiasServico();
            $listaNoticiasCategoria = $noticiasServico->listarPorCategoria($categoriaId);
        }
    } catch (Throwable $e) {
        Utils::registrarLog($e);
        $mensagemErro = "Não foi possível carregar as notícias desta categoria.";
    }
}

require_once __DIR__ . '/includes/cabecalho.php';
?>

<div class="row my-1 mx-md-n1">
    <article class="col-12">
        <h2 class="text-center">
            Notícias sobre
            <?php if ($categoriaSelecionada !== null): ?>
                <span class="badge bg-primary"><?= Utils::escaparHtml($categoriaSelecionada['nome']) ?></span>
            <?php endif; ?>
        </h2>

        <?php if ($mensagemErro !== ""): ?>
            <p class="alert alert-warning text-center"><?= Utils::escaparHtml($mensagemErro) ?></p>
        <?php elseif ($listaNoticiasCategoria === []): ?>
            <p class="text-center text-muted">Não há notícias cadastradas nesta categoria.</p>
        <?php else: ?>
            <div class="row my-1">
                <div class="col-12 px-md-1">
                    <div class="list-group">
                        <?php foreach ($listaNoticiasCategoria as $noticia): ?>
                            <?php
                            $autor = trim((string) ($noticia['autor'] ?? ""));
                            if ($autor === "") {
                                $autor = "Equipe Microblog";
                            }
                            ?>
                            <a href="noticia.php?id=<?= Utils::escaparHtml($noticia['id']) ?>" class="list-group-item list-group-item-action">
                                <h3 class="fs-6"><?= Utils::escaparHtml($noticia['titulo']) ?></h3>
                                <p>
                                    <time datetime="<?= Utils::escaparHtml(date('Y-m-d', strtotime($noticia['data']))) ?>"><?= Utils::escaparHtml(date('d/m/Y', strtotime($noticia['data']))) ?></time>
                                    - <?= Utils::escaparHtml($autor) ?>
                                </p>
                                <p><?= Utils::escaparHtml($noticia['resumo']) ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </article>
</div>

<?php
require_once __DIR__ . '/includes/todas.php';
require_once __DIR__ . '/includes/rodape.php';
?>
