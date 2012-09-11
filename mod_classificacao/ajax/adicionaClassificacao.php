<?php
if($_POST){
    $ret['status'] = 0;
    
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
    
    if($id_materia <= 0){
        $ret['msg'] = "Selecione um Matéria para continuar";
        echo json_encode($ret);
        die;
    }
    
    if($id_subitem > 0 && ($id_item <= 0 || $id_topico <= 0 || $id_divisao <= 0)){
        $ret['msg'] = "Selecione um todos os combos para continuar";
        echo json_encode($ret);
        die;
    }
    
    if($id_item > 0 && ($id_topico <= 0 || $id_divisao <= 0)){
        $ret['msg'] = "Selecione um todos os combos para continuar";
        echo json_encode($ret);
        die;
    }
    
    if($id_topico > 0 && $id_divisao <= 0){
        $ret['msg'] = "Selecione um todos os combos para continuar";
        echo json_encode($ret);
        die;
    }
    
    require_once '../class/classificacao.php';
    
    $classificacao = new Classificacao();
    
    $classificacao->__set("ID_BCO_QUESTAO", $id_questao);
    $classificacao->__set("ID_MATERIA", $id_materia);
    $classificacao->__set("ID_DIVISAO", $id_divisao);
    $classificacao->__set("ID_TOPICO", $id_topico);
    $classificacao->__set("ID_ITEM", $id_item);
    $classificacao->__set("ID_SUBITEM", $id_subitem);
    
    $rs_adicionar = $classificacao->adicionar(1);
    
    if($rs_adicionar->status){
        $ret['status']  = 1;
        $ret['id']      = $rs_adicionar->id;
        
        $ret['id_materia']  = $id_materia;
        $ret['id_divisao']  = $id_divisao;
        $ret['id_topico']   = $id_topico;
        $ret['id_item']     = $id_item;
        $ret['id_subitem']  = $id_subitem;
    }
    
    $ret['msg'] = $rs_adicionar->msg;
    
    echo json_encode($ret);
}
?>
