<?php
namespace Microblog\Services;

use Microblog\Database\ConexaoBD;
use Microblog\Models\Noticia;
use PDO;

final final class NoticiasServico 
{
   private PDO $conexao; 

   public function __construct()
   {
    $this->conexao = ConexaoBD::getConexao();
   }

   public function inserir(Noticia $noticia):void
   {
    $sql = "INSERT INT noticias (titulo, texto, resumo, imagem, destaque, usuario_id, categoria_id) VALUES( :titulo, :texto, :resumo, :imagem, :destaque, :usuario_id, :categoria_id)";
   }
}
