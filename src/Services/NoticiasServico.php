<?php
namespace Microblog\Services;

use Exception;
use Microblog\Database\ConexaoBD;
use Microblog\Enums\TipoUsuario;
use Microblog\Helpers\Utils;
use Microblog\Models\Noticia;
use PDO;
use PDOException;

final class NoticiasServico
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = ConexaoBD::getConexao();
    }

    public function listarTodos(int $usuarioId, string $tipoUsuario): array
    {
        $sql = "SELECT n.id, n.data, n.titulo, n.texto, n.resumo, n.imagem, n.destaque, n.usuario_id, n.categoria_id";

        if ($tipoUsuario === TipoUsuario::ADMIN->value) {
            $sql .= ", u.nome AS autor";
            $sql .= " FROM noticias n";
            $sql .= " INNER JOIN usuarios u ON u.id = n.usuario_id";
            $sql .= " ORDER BY n.data DESC";
        } else {
            $sql .= " FROM noticias n";
            $sql .= " WHERE n.usuario_id = :usuario_id";
            $sql .= " ORDER BY n.data DESC";
        }

        try {
            $consulta = $this->conexao->prepare($sql);

            if ($tipoUsuario !== TipoUsuario::ADMIN->value) {
                $consulta->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
            }

            $consulta->execute();
            return $consulta->fetchAll();
        } catch (PDOException $e) {
            Utils::registrarLog($e);
            throw new Exception("Erro ao listar notícias");
        }
    }

    public function inserir(Noticia $noticia): void
    {
        $sql = "INSERT INTO noticias (
            titulo, texto, resumo, imagem, destaque, usuario_id, categoria_id
        ) VALUES(
            :titulo, :texto, :resumo, :imagem, :destaque, :usuario_id, :categoria_id
        )";

        try {
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":titulo", $noticia->getTitulo());
            $consulta->bindValue(":texto", $noticia->getTexto());
            $consulta->bindValue(":resumo", $noticia->getResumo());
            $consulta->bindValue(":imagem", $noticia->getImagem());
            $consulta->bindValue(":destaque", $noticia->getDestaque()->value);
            $consulta->bindValue(":usuario_id", $noticia->getUsuarioId());
            $consulta->bindValue(":categoria_id", $noticia->getCategoriaId());
            $consulta->execute();
        } catch (PDOException $e) {
            Utils::registrarLog($e);
            throw new Exception("Erro ao inserir noticia");
        }
    }
}
