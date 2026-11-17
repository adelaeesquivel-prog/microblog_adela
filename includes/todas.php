<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Microblog\Enums\TipoUsuario;
use Microblog\Helpers\Utils;
use Microblog\Services\NoticiasServico;

if (!isset($erroAoCarregarNoticias)) {
    $erroAoCarregarNoticias = false;
}

if (!isset($listaDeNoticias)) {
    $listaDeNoticias = [];
    $erroAoCarregarNoticias = false;

    try {
        $noticiasServico = new NoticiasServico();
        $listaDeNoticias = $noticiasServico->listarTodos(0, TipoUsuario::ADMIN->value);
    } catch (Throwable $e) {
        Utils::registrarLog($e);
        $erroAoCarregarNoticias = true;
    }
}
?>

<hr class="my-5 w-50 mx-auto">

<div class="row my-1">
    <div class="col-12 px-md-1">
        <div class="list-group">
            <h2 class="fs-6 text-center text-muted">Todas as notícias</h2>

            <?php if ($erroAoCarregarNoticias): ?>
                <p class="list-group-item text-danger text-center">Não foi possível carregar as notícias.</p>
            <?php elseif ($listaDeNoticias === []): ?>
                <p class="list-group-item text-muted text-center">Não há notícias cadastradas.</p>
            <?php else: ?>
                <?php foreach ($listaDeNoticias as $noticia): ?>
                    <a href="noticia.php?id=<?= Utils::escaparHtml($noticia['id']) ?>" class="list-group-item list-group-item-action">
                        <h3 class="fs-6"><time><?= Utils::escaparHtml(date('d/m/Y', strtotime($noticia['data']))) ?></time> - <?= Utils::escaparHtml($noticia['titulo']) ?></h3>
                        <p><?= Utils::escaparHtml($noticia['resumo']) ?></p>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
