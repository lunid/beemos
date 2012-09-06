<?php
if($_POST){
    require_once '../class/materia.php';
    require_once '../class/divisao.php';
    require_once '../class/topico.php';
    require_once '../class/item.php';
    require_once '../class/subitem.php';
    
    $tipo           = strtolower(trim($_POST['tipo']));
    $classificacao  = (int)$_POST['classificacao'];
    $valor          = (int)$_POST['valor'];
    $id_materia     = (int)$_POST['id_materia'];
    $id_divisao     = (int)$_POST['id_divisao'];
    $id_topico      = (int)$_POST['id_topico'];
    $id_item        = (int)$_POST['id_item'];
    $id_subitem     = (int)$_POST['id_subitem'];
            
    switch($tipo){
        case 'id_materia':
            $materia = new Materia();
            echo $materia->carregaCombo($classificacao, $valor);
            break;
        case 'id_divisao':
            $divisao = new Divisao();
            echo $divisao->carregaCombo($classificacao, $valor, $id_materia);
            break;
        case 'id_topico':
            $topico = new Topico();
            echo $topico->carregaCombo($classificacao, $valor, $id_materia, $id_divisao);
            break;
        case 'id_item':
            $item = new Item();
            echo $item->carregaCombo($classificacao, $valor, $id_materia, $id_divisao, $id_topico);
            break;
        case 'id_subitem':
            $subitem = new Subitem();
            echo $subitem->carregaCombo($classificacao, $valor, $id_materia, $id_divisao, $id_topico, $id_item);
            break;
    }
}
?>
