<?php
namespace Microblog\Auth;

/*Sessoes no PHP
Funcionalidade usada para controlar o acesso para determinadas página e recursos de um site/aplicação.

Exemplos: áreas administrativas, painel de controle, área de cliente/aluno   

Nessas áreas, o acesso depende de alguma forma de autenticação, como login e senha, biometria por dois fatores etc. */

final  class ControleDeAcesso 
{    
    //Impedir a criação de objeto/instancia
    private function __construct(){}
    
    //inicia a sessao se ela ainda não estiver ativa
    private static function iniciarSessao():void
    {
        //Se o status da sessao NÃO FOR ativa (ou se a sessao esta inativa)
        if(session_status() !== PHP_SESSION_ACTIVE){

            //Então, iniciamos a sessao
            session_start();
         }
    }

    private static function encerrarSessao(): void
    {
        $_SESSION = [];  //limpar os dados do usuario na sessão
        session_destroy();  //destroi/removequalque dado da sessão do navegador
    }

    public  static function exigirLogin():void
    {
        self::iniciarSessao();

        //Se não existir na sessão uma variavel chamada id,
        //signiifica que o usuario não esta logado
        if(!isset($_SESSION['id'])){

            //encerrar a sessão(ainda programar)
            self::encerrarSessao();

          //redirecionar para a pagina login
            header("location:../login.php?acesso_proibido");
            exit;
        }
    }

    public static function login(int $id, string $nome, string  $tipo):void
    {
        self::iniciarSessao();

        //Criando as variaveis de sessão com alguns dados de quem esta logado
        $_SESSION['id'] = $id;
        $_SESSION['nome'] = $nome;
        $_SESSION['tipo'] = $tipo;
    }

    public static function logout(): void
    {
        self:: iniciarSessao();
        self::encerrarSessao();
        header("Location:../login-php?logout");
        exit;
    }

}
