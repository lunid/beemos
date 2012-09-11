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
    
    $id_materia_or      = (int)$_POST['id_materia_or'];
    $id_divisao_or      = (int)$_POST['id_divisao_or'];
    $id_topico_or       = (int)$_POST['id_topico_or'];
    $id_item_or         = (int)$_POST['id_item_or'];
    $id_subitem_or      = (int)$_POST['id_subitem_or'];
    
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
    
    if($id_materia <= 0 && $id_materia_or <= 0){
        $ret['msg'] = "Selecione um Matéria para continuar";
        echo json_encode($ret);
        die;
    }else{
        $id_materia = $id_materia == 0 ? $id_materia_or : $id_materia;
    }
    
    if($id_subitem != $id_subitem_or && $id_subitem > 0){
        $id_item    = $id_item > 0 && $id_item != $id_item_or ? $id_item : $id_item_or;
        $id_topico  = $id_topico > 0 && $id_topico != $id_topico_or ? $id_topico : $id_topico_or;
        $id_divisao = $id_divisao > 0 && $id_divisao != $id_divisao_or ? $id_divisao : $id_divisao_or;
    }
    
    if($id_item != $id_item_or && $id_item > 0){
        $id_subitem = $id_subitem > 0 && $id_subitem != $id_subitem_or ? $id_subitem : 0;
        $id_topico  = $id_topico > 0 && $id_topico != $id_topico_or ? $id_topico : $id_topico_or;
        $id_divisao = $id_divisao > 0 && $id_divisao != $id_divisao_or ? $id_divisao : $id_divisao_or;
    }
    
    if($id_topico != $id_topico_or && $id_topico > 0){
        $id_item    = $id_item > 0 && $id_item != $id_item_or ? $id_item : 0;
        $id_subitem = $id_subitem > 0 && $id_subitem != $id_subitem_or ? $id_subitem : 0;
        $id_divisao = $id_divisao > 0 && $id_divisao != $id_divisao_or ? $id_divisao : $id_divisao_or;
    }
    
    if($id_divisao != $id_divisao && $id_divisao > 0){
        $id_item    = $id_item > 0 && $id_item != $id_item_or ? $id_item : 0;
        $id_subitem = $id_subitem > 0 && $id_subitem != $id_subitem_or ? $id_subitem : 0;
        $id_topico  = $id_topico > 0 && $id_topico != $id_topico_or ? $id_topico : 0;
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
    
    $ret_alteracao = $classificacao->alterar(1);
    
    if($ret_alteracao->status){
        $ret['status']  = 1;
        
        $ret['id_materia']  = $id_materia;
        $ret['id_divisao']  = $id_divisao;
        $ret['id_topico']   = $id_topico;
        $ret['id_item']     = $id_item;
        $ret['id_subitem']  = $id_subitem;
    }
    
    $ret['msg'] = $ret_alteracao->msg;
    
    echo json_encode($ret);
}
?>
