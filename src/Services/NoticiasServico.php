<?php
namespace Microblog\Services;

use Exception;
use InvalidArgumentException;
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
            $sql .= " LEFT JOIN usuarios u ON u.id = n.usuario_id";
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

    public function listarPorCategoria(int $categoriaId): array
    {
        $sql = "SELECT n.id, n.data, n.titulo, n.resumo, n.texto, n.imagem, u.nome AS autor
                FROM noticias n
                LEFT JOIN usuarios u ON u.id = n.usuario_id
                WHERE n.categoria_id = :categoria_id
                ORDER BY n.data DESC";

        try {
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":categoria_id", $categoriaId, PDO::PARAM_INT);
            $consulta->execute();
            return $consulta->fetchAll();
        } catch (PDOException $e) {
            Utils::registrarLog($e);
            throw new Exception("Erro ao listar notícias por categoria");
        }
    }

    public function buscarPorId(int $usuarioId, string $tipoUsuario, int $noticiaId): ?array
    {
        $sql = "SELECT n.id, n.data, n.titulo, n.texto, n.resumo, n.imagem, n.destaque,
                       n.usuario_id, n.categoria_id, u.nome AS autor, c.nome AS categoria
                FROM noticias n
                LEFT JOIN usuarios u ON u.id = n.usuario_id
                LEFT JOIN categorias c ON c.id = n.categoria_id
                WHERE n.id = :noticia_id";

        if ($tipoUsuario !== TipoUsuario::ADMIN->value) {
            $sql .= " AND n.usuario_id = :usuario_id";
        }

        try {
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":noticia_id", $noticiaId, PDO::PARAM_INT);

            if ($tipoUsuario !== TipoUsuario::ADMIN->value) {
                $consulta->bindValue(":usuario_id", $usuarioId, PDO::PARAM_INT);
            }

            $consulta->execute();
            return $consulta->fetch() ?: null;
        } catch (PDOException $e) {
            Utils::registrarLog($e);
            throw new Exception("Erro ao buscar notícia por ID");
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

    public function atualizar(Noticia $noticia, int $usuarioId, string $tipoUsuario): void
    {
        $noticiaId = $noticia->getId();
        if ($noticiaId === null) {
            throw new InvalidArgumentException("Não é possível atualizar uma notícia sem ID.");
        }

        $sql = "UPDATE noticias
                SET titulo = :titulo,
                    texto = :texto,
                    resumo = :resumo,
                    imagem = :imagem,
                    destaque = :destaque,
                    categoria_id = :categoria_id
                WHERE id = :noticia_id";

        if ($tipoUsuario !== TipoUsuario::ADMIN->value) {
            $sql .= " AND usuario_id = :usuario_id";
        }

        try {
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":titulo", $noticia->getTitulo());
            $consulta->bindValue(":texto", $noticia->getTexto());
            $consulta->bindValue(":resumo", $noticia->getResumo());
            $consulta->bindValue(":imagem", $noticia->getImagem());
            $consulta->bindValue(":destaque", $noticia->getDestaque()->value);
            $consulta->bindValue(":categoria_id", $noticia->getCategoriaId(), PDO::PARAM_INT);
            $consulta->bindValue(":noticia_id", $noticiaId, PDO::PARAM_INT);

            if ($tipoUsuario !== TipoUsuario::ADMIN->value) {
                $consulta->bindValue(":usuario_id", $usuarioId, PDO::PARAM_INT);
            }

            $consulta->execute();

            if ($consulta->rowCount() === 0) {
                $sqlVerificacao = "SELECT 1 FROM noticias WHERE id = :noticia_id";
                if ($tipoUsuario !== TipoUsuario::ADMIN->value) {
                    $sqlVerificacao .= " AND usuario_id = :usuario_id";
                }

                $verificacao = $this->conexao->prepare($sqlVerificacao);
                $verificacao->bindValue(":noticia_id", $noticiaId, PDO::PARAM_INT);

                if ($tipoUsuario !== TipoUsuario::ADMIN->value) {
                    $verificacao->bindValue(":usuario_id", $usuarioId, PDO::PARAM_INT);
                }

                $verificacao->execute();
                if ($verificacao->fetchColumn() === false) {
                    throw new Exception("Notícia não encontrada ou acesso não permitido");
                }
            }
        } catch (PDOException $e) {
            Utils::registrarLog($e);
            throw new Exception("Erro ao atualizar notícia");
        }
    }

    public function excluir(int $usuarioId, string $tipoUsuario, int $noticiaId): void
    {
        $sql = "DELETE FROM noticias WHERE id = :noticia_id";

        if ($tipoUsuario !== TipoUsuario::ADMIN->value) {
            $sql .= " AND usuario_id = :usuario_id";
        }

        try {
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(":noticia_id", $noticiaId, PDO::PARAM_INT);

            if ($tipoUsuario !== TipoUsuario::ADMIN->value) {
                $consulta->bindValue(":usuario_id", $usuarioId, PDO::PARAM_INT);
            }

            $consulta->execute();

            if ($consulta->rowCount() === 0) {
                throw new Exception("Notícia não encontrada ou acesso não permitido");
            }
        } catch (PDOException $e) {
            Utils::registrarLog($e);
            throw new Exception("Erro ao excluir notícia");
        }
    }
}
