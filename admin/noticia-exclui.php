<?php

use Microblog\Auth\ControleDeAcesso;
use Microblog\Helpers\Utils;
use Microblog\Services\NoticiasServico;

require_once __DIR__ . '/../vendor/autoload.php';

ControleDeAcesso::exigirLogin();

$noticiaId = (int) Utils::normalizarEntrada($_GET['id'] ?? null, 'inteiro');
Utils::verificarId($noticiaId);

try {
    $noticiasServico = new NoticiasServico();
    $noticiasServico->excluir(
        (int) $_SESSION['id'],
        (string) $_SESSION['tipo'],
        $noticiaId
    );

    header("location:noticias.php");
    exit;
} catch (Throwable $e) {
    Utils::registrarLog($e);
    Utils::alertaErro("Não foi possível excluir a notícia. Verifique se ela existe e se você tem permissão.");
}
