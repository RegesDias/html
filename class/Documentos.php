<?php
require_once('Generica.php');
class Documentos extends Generica{
    public static $sql = "SELECT DISTINCT
                                tb_documentos.id,
                                tb_documentos.ano_documento,
                                tb_documentos.numero_documento,
                                tb_documentos.assunto,
                                tb_documentos.origem,
                                tb_documentos.data_inclusao,
                                tb_documentos.status,
                                tb_tipo.sigla as sigla,
                                tb_movimentacao.data_recebido,
                                tb_movimentacao.setor_id,
                                tb_movimentacao.id as idMovimentacao
                            FROM
                                tb_documentos
                            LEFT JOIN
                                tb_movimentacao
                                ON tb_documentos.id = tb_movimentacao.documento_id
                            LEFT JOIN
                                tb_tipo
                                ON tb_tipo.id = tb_documentos.tipo
                                WHERE ";

    public function buscaId($id){
        $sql = "SELECT DISTINCT
                                tb_documentos.id,
                                tb_documentos.ano_documento,
                                tb_documentos.numero_documento,
                                tb_documentos.assunto,
                                tb_documentos.origem,
                                tb_documentos.data_inclusao,
                                tb_status.nome as status,
                                usuario.nome as resposavel,
                                tb_tipo.sigla as sigla,
                                tb_tipo.nome as tipo,
                                tb_movimentacao.data_entrada,
                                tb_movimentacao.setor_id,
                                tb_movimentacao.id as idMovimentacao
                            FROM
                                controle_docs_teste.tb_documentos
                            LEFT JOIN
                                controle_docs_teste.tb_movimentacao
                                ON tb_documentos.id = tb_movimentacao.documento_id
                            LEFT JOIN
                                controle_docs_teste.tb_status
                                ON tb_status.id = tb_documentos.status
                            LEFT JOIN
                                gespes.usuario
                                ON gespes.usuario.id = tb_movimentacao.usuario_id
                            LEFT JOIN
                                controle_docs_teste.tb_tipo
                                ON tb_tipo.id = tb_documentos.tipo
                            WHERE 
                                tb_documentos.id = '$id'";
        return $exec = Conexao::InstControle()->prepare($sql);
    }
    public function buscaNumeroAnoTipoStatusLocal($ano,$tipo, $status,$idSetor,$order){
        $sql = self::$sql." tb_movimentacao.ativo = '1' ";
        if(($ano != '') AND ($ano !='tds')){
            $sql .= " AND tb_documentos.ano_documento = '$ano' ";
        }
        if(($status != '') AND ($status !='tds')){
            $sql .= " AND tb_documentos.status = '$status' ";
        }
        if(($tipo != '') AND ($tipo !='tds')){
            $sql .= " AND tb_documentos.tipo = '$tipo'";
        }
        if(($idSetor != '') AND ($idSetor !='tds')){
            if($idSetor =='user'){
                $usuario_id = $_SESSION['id'];
                $sql .= " AND tb_movimentacao.usuario_id = '$usuario_id'";
            }else{
                $sql .= " AND tb_movimentacao.setor_id = '$idSetor'";
            }
        }
        if(($order != '')AND ($order != 'NAO')){
            $sql .= " ORDER BY ".$order." DESC ";
        }
        return $exec = Conexao::InstControle()->prepare($sql);
    }
    public function buscaNumeroAno($numero,$ano){
        if($numero != ''){
            $sql = self::$sql .= " numero_documento = '$numero' ";
        }
        if($ano != ''){
            if($numero != ''){
                $sql = self::$sql .= " AND "; 
            }
            $sql = self::$sql .= " ano_documento = '$ano' ";
        }
        return $exec = Conexao::InstControle()->prepare($sql);
    }
    public function buscaAssunto($assunto, $order){
        if($assunto != ''){
            $sql = self::$sql .= " assunto like '%$assunto%' 
                ORDER BY $order DESC";
        }
        return $exec = Conexao::InstControle()->prepare($sql);
    }

    public function buscaAnos(){
        $sql = "SELECT DISTINCT ano_documento FROM tb_documentos ORDER BY ano_documento ASC";
        $exec = Conexao::InstControle()->prepare($sql);
        $exec->execute();
        $result = $exec->fetchAll(PDO::FETCH_ASSOC);
        $maiorAnoAtual = $result[0]['ano_documento'];
        $anoAtual = date("Y");
        if($maiorAnoAtual < $anoAtual){
            $novo['ano_documento'] = $anoAtual;
            array_unshift($result, $novo);
        }
        return $result;
    }
    public function recebe($idMovimentacao){
        $idUser = $_SESSION['id'];
        $sql = "UPDATE tb_movimentacao SET 
                        usuario_id = '$idUser', 
                        data_recebido = NOW() 
                WHERE 
                    id = '$idMovimentacao'";
        $stm = Conexao::InstControle()->prepare($sql);
        $stm->execute();
        $retorno = array('codigo' => 1, 'mensagem' => 'Documento recebido com sucesso!');
        echo json_encode($retorno);
        exit();
    }
    public function movimentarExecutar($idDocumento,$encaminharResponsavel,$movimentacoesSetor,$encaminharTexto){
        $idUser = $_SESSION['id'];
        $sql = "INSERT INTO tb_movimentacao(
                                    documento_id,
                                    usuario_id,
                                    setor_id,
                                    encaminhamento,
                                    ativo,
                                    log_user_id,
                                    data_entrada
                                )VALUES(
                                    '$idDocumento',
                                    '$encaminharResponsavel',
                                    '$movimentacoesSetor',
                                    '$encaminharTexto',
                                    '1',
                                    '$idUser',
                                    NOW())";
                $stm = Conexao::InstControle()->prepare($sql);
                $stm->execute();
    }
    public function finalizarMovimento($idMovimentacao){
        $sql = "UPDATE tb_movimentacao SET
                        ativo = 0,
                        data_saida = NOW()
                WHERE 
                    id = '$idMovimentacao'";
        $stm = Conexao::InstControle()->prepare($sql);
        $stm->execute();
    }
    
    
}
?>