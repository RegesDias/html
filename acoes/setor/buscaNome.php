<?php
header('Content-Type: application/json');

require_once('../../class/Setor.php');
$dado = $_GET['dado'];
$s = new Setor;
$s->verificaPreenchimentoCampo($dado, 'busca');
$exec = $s->buscaNome($dado);

if(Conexao::verificaLogin('setor')){
    $exec->execute();
    if ($exec->rowCount() >= 1) {
        $s->gravaLog('Busca setor: '.$dado);
        echo json_encode($exec->fetchAll(PDO::FETCH_ASSOC));
    } else {
        $retorno = array('codigo' => 0, 'mensagem' => 'Nenhum setor encontrado!');
        echo json_encode($retorno);
    }
}