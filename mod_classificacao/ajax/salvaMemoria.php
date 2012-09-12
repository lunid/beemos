<?php
if($_POST){
    $id_materia     = (int)$_POST['id_materia'];
    $txt_materia    = $_POST['txt_materia'];
    $id_divisao     = (int)$_POST['id_divisao'];
    $txt_divisao    = $_POST['txt_divisao'];
    $id_topico      = (int)$_POST['id_topico'];
    $txt_topico     = $_POST['txt_topico'];
    $id_item        = (int)$_POST['id_item'];
    $txt_item       = $_POST['txt_item'];
    $id_subitem     = (int)$_POST['id_subitem'];
    $txt_subitem    = $_POST['txt_subitem'];
    
    $cookie_value   = $id_materia.",".$txt_materia.",".$id_divisao.",".$txt_divisao.",".$id_topico.",".$txt_topico.",".$id_item.",".$txt_item.",".$id_subitem.",".$txt_subitem;
    
    $expire = 60 * 60 * 24 * 60 + time(); 
    
    setcookie('memoria_questao', $cookie_value, $expire, "/");
    
    $ret['status']  = 1;
    $ret['msg']     = "Cookie salvo com sucesso!";
    
    echo json_encode($ret);
}
?>
