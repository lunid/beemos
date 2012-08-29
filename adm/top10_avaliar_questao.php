<?
    //====================================================================================================================//
    //======================================== Valida Acesso do Usuário na Página ========================================//
    //====================================================================================================================//

    session_start();
    
    require_once 'class/usuario.php';
    require_once 'class/questoes.php';
    
    if(isset($_SESSION['ADM_USUARIO'])){
        //Valida permissão de acesso ao Usuário
        $ret = Usuario::validaAcesso(unserialize($_SESSION['ADM_USUARIO']));
        
        if(!$ret->status){
            //redirecionando para efetuar login
            header("Location: index.php");  
        }
    }else{
        //redirecionando para efetuar login
        header("Location: index.php");
    }
    
    //Armazena instância do objeto usuário
    $usuario = unserialize($_SESSION['ADM_USUARIO']);
    
    //Inicia o valor de id_materia enviado via GET
    $id_materia = @(int)$_GET['id_materia'];
    
    //Inicia o valor de id_materia enviado via GET
    $id_questao = @(int)$_GET['id_questao'];
    
    //Inicia a instância do objeto Questoes
    $questoes = new Questoes($id_questao);
    
    //====================================================================================================================//
    //======================================== Valida Acesso do Usuário na Página ========================================//
    //====================================================================================================================//
?>
<!DOCTYPE html>
<html>
    <head>
        <title>ADM Interbits | TOP 10 | Avaliar Questões</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <script type="text/javascript" src="../js/libs/jquery_171.js"></script>
        
        <link rel="stylesheet" type="text/css" media="screen" href="../js/libs/jqgrid/themes/redmond/jquery-ui-custom.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="../js/libs/jqgrid/themes/ui.jqgrid.css" />

        <script src="../js/libs/jqgrid/js/i18n/grid.locale-pt-br.js" type="text/javascript"></script>
        <script src="../js/libs/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
    </head>
    <body>
        <h1>Questão</h1>
        <? 
            if($usuario->validaUsuarioAvaliacao(@(int)$_GET['id_materia'])){ 
                $rs = $questoes->listaQuestoesTop10Materia((int)$_GET['id_materia']);
                
                if(sizeof($rs) > 0){
                    $pos=1;
        ?>
        <br />
        <table cellpadding="4" cellspacing="0" border="1">
            <tr>
                <th>
                    Posição
                </th>
                <th>
                    Questão
                </th>
                <th>
                    Total / Uso
                </th>
                <th>
                    &nbsp;
                </th>  
            </tr>
            <? foreach($rs as $questao){ ?>
            <tr>
                <td>
                    <?=$pos?>&ordf;
                </td>
                <td>
                    <?=$questao->getIdBcoQuestao()?>
                </td>
                <td>
                    <?=$questao->getTotalUso()?>
                </td>
                <td>
                    <a href="top10_avaliar_questao.php?id_questao=<?=$questao->getIdBcoQuestao()?>" target="_blank">
                        <img src="../img/icone_avaliar.gif" border="0" style="width:23px;height:23px;" />
                    </a>
                </td>
            </tr>
            <? $pos++; } ?>
        </table>
        <?
                }else{
        ?>
        <h1>Nenhuma Questão encontrada.</h1>
        <?
                }
            }else{ 
        ?>
            <h1>Você não possui permissão para avaliar as questões desta matéria</h1>
        <? 
            } 
        ?>
    </body>
</html>
