<?php

use Microblog\Helpers\Utils;
use Microblog\Services\CategoriaServico;
use Microblog\Services\UsuarioServico;

require_once __DIR__ . '/../vendor/autoload.php';

$id =Utils::normalizarEntrada($_GET['id'] ?? null, 'inteiro');
Utils::verificarId($id);
try{
    $usuario =  new UsuarioServico();
    $usuario->excluir($id);
    header("location:usuario.php");
    exit;
}catch (Throwable $e) {
    Utils::registrarLog($e);
    Utils::alertaErro("Não foi possivel excluir o usuario. Tente novamente");
}

