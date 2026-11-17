<?php
use Microblog\Enums\TipoUsuario;
use Microblog\Helpers\Utils;
use Microblog\Services\NoticiasServico;

require_once __DIR__ . '/vendor/autoload.php';

$noticia = null;
$mensagemErro = '';
$noticiaId = Utils::normalizarEntrada($_GET['id'] ?? null, 'inteiro');

if ($noticiaId <= 0) {
    $mensagemErro = 'Notícia não encontrada. Verifique o endereço e tente novamente.';
} else {
    try {
        $noticiasServico = new NoticiasServico();
        $noticia = $noticiasServico->buscarPorId(0, TipoUsuario::ADMIN->value, $noticiaId);

        if ($noticia === null) {
            $mensagemErro = 'Notícia não encontrada. Verifique o endereço e tente novamente.';
        }
    } catch (Throwable $e) {
        Utils::registrarLog($e);
        $mensagemErro = 'Não foi possível carregar esta notícia no momento.';
    }
}

require_once __DIR__ . '/includes/cabecalho.php';
?>

<div class="row my-1 mx-md-n1">
    <article class="col-12">
        <?php if ($mensagemErro !== ''): ?>
            <div class="alert alert-warning text-center" role="alert">
                <?= Utils::escaparHtml($mensagemErro) ?>
            </div>
        <?php else: ?>
            <?php
            $autor = trim((string) ($noticia['autor'] ?? ''));
            if ($autor === '') {
                $autor = 'Equipe Microblog';
            }
            $dataNoticia = (string) $noticia['data'];
            ?>
            <h2><?= Utils::escaparHtml($noticia['titulo']) ?></h2>
            <p class="font-weight-light">
                <time datetime="<?= Utils::escaparHtml(str_replace(' ', 'T', $dataNoticia)) ?>"><?= Utils::escaparHtml(date('d/m/Y', strtotime($dataNoticia))) ?></time> -
                <span><?= Utils::escaparHtml($autor) ?></span>
            </p>
            <img src="images/<?= Utils::escaparHtml($noticia['imagem']) ?>" alt="<?= Utils::escaparHtml($noticia['titulo']) ?>" class="float-start pe-2 img-fluid">
            <p class="ajusta-texto"><?= nl2br(Utils::escaparHtml($noticia['texto'])) ?></p>
        <?php endif; ?>
    </article>
</div>

<?php
require_once __DIR__ . '/includes/todas.php';
require_once __DIR__ . '/includes/rodape.php';
?>
