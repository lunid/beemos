<?php
if($_POST){
    $ret['status'] = 0;
    
    $id_classificacao   = (int)$_POST['id_classificacao'];
    $id_questao         = (int)$_POST['id_questao'];
    $id_materia         = (int)$_POST['id_materia'];
    $id_divisao         = (int)$_POST['id_divisao'];
    $id_topico          = (int)$_POST['id_topico'];
    $id_item            = (int)$_POST['id_item'];
    $id_subitem         = (int)$_POST['id_subitem'];
    
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
    
    if($id_materia <= 0){
        $ret['msg'] = "Código da Matéria inválido!";
        echo json_encode($ret);
        die;
    }
    
    if($id_materia <= 0 && $id_divisao > 0){
        $ret['msg'] = "Selecione um Matéria para continuar";
        echo json_encode($ret);
        die;
    }
    
    require_once '../class/classificacao.php';
    
    $classificacao = new Classificacao();
    
    $classificacao->__set("ID_CLASSIFICACAO", $id_classificacao);
    $classificacao->__set("ID_BCO_QUESTAO", $id_questao);
    $classificacao->__set("ID_MATERIA", $id_materia);
    $classificacao->__set("ID_DIVISAO", $id_divisao);
    $classificacao->__set("ID_TOPICO", $id_topico);
    $classificacao->__set("ID_ITEM", $id_item);
    $classificacao->__set("ID_SUBITEM", $id_subitem);
    
    if($classificacao->alterar()){
        $ret['status']  = 1;
        $ret['msg']     = "Classificação salva com sucesso!";
    }else{
        $ret['msg'] = "Falaha ao salvar classificação. Tente mais tarde.";
    }
    
    echo json_encode($ret);
}
?>
