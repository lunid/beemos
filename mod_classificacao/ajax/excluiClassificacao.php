<?php
if($_POST){
    $ret['status'] = 0;
    
    $id_classificacao   = (int)$_POST['id_classificacao'];
    $id_questao         = (int)$_POST['id_questao'];
    
    if($id_questao <= 0){
        $ret['msg'] = "Código da Questão inválido!";
        echo json_encode($ret);
        die;
    }
    
    if($id_classificacao <= 0){
        $ret['msg'] = "Código da Classificação inválido!";
        echo json_encode($ret);
        die;
    }
    
    require_once '../class/classificacao.php';
    
    $classificacao = new Classificacao();
    
    $classificacao->__set("ID_CLASSIFICACAO", $id_classificacao);
    $classificacao->__set("ID_BCO_QUESTAO", $id_questao);
    
    $ret_excluir = $classificacao->excluir();
    
    if($ret_excluir->status){
        $ret['status']  = 1;
        $ret['msg']     = "Classificação excluída com sucesso!";
    }else{
        $ret['msg'] = $ret_excluir->msg;
    }
    
    echo json_encode($ret);
}
?>
