<?php
if($_POST){
    $ret['status'] = 0;
    
    $id_usuario   = (int)$_POST['id_usuario'];
    $id_questao   = (int)$_POST['id_questao'];
    
    if($id_usuario <= 0){
        $ret['msg'] = "Código do usuário inválido!";
        echo json_encode($ret);
        die;
    }
    
    require_once '../class/classificacao.php';
    
    $classificacao = new Classificacao();
    
    $ret_mudancas = $classificacao->salvarMudancas($id_usuario, $id_questao);
    
    if($ret_mudancas->status){
        $ret['status']  = 1;
    }
    
    $ret['msg'] = $ret_mudancas->msg;
    
    echo json_encode($ret);
}
?>
