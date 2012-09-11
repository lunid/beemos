<?php
if($_POST){
    $id_materia = (int)$_POST['id_materia'];
    $id_divisao = (int)$_POST['id_divisao'];
    $id_topico  = (int)$_POST['id_topico'];
    $id_item    = (int)$_POST['id_item'];
    $id_subitem = (int)$_POST['id_subitem'];
    
    $cookie_value = $id_materia.",".$id_divisao.",".$id_topico.",".$id_item.",".$id_subitem;
    
    $expire = 60 * 60 * 24 * 60 + time(); 
    
    setcookie('memoria_questao', $cookie_value, $expire, "/");
    
    $ret['status']  = 1;
    $ret['msg']     = "Cookie salvo com sucesso!";
    
    echo json_encode($ret);
}
?>
