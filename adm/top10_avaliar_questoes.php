<?
    //====================================================================================================================//
    //======================================== Valida Acesso do Usuário na Página ========================================//
    //====================================================================================================================//

    session_start();
    
    require_once 'class/usuario.php';
    require_once 'class/questoes.php';
    require_once 'class/materia.php';
    require_once 'class/fonte.php';
    
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
    
    //Inicia a instância do objeto Questoes
    $questoes = new Questoes();
    
    if($_POST){
        if($_POST['hdd_acao'] == 'usuario_questao'){
            if($questoes->alteraUsuarioQuestao($_POST['id_questao'], $_POST['id_usuario'])){
                $json['msg'] = 'Usuário alterado com sucesso';
            }else{
                $json['msg'] = 'Falha na tentativa de alterar usuário';
            }
            echo json_encode($json);
            die;
        }
    }
    
    //Armazena instância do objeto usuário
    $usuario = unserialize($_SESSION['ADM_USUARIO']);
    
    //Inicia valor de id_materia enviado via GET
    $id_materia = @(int)$_GET['id_materia'];
    
    //Inicia valor de id_fonte enviado via GET
    $id_fonte_vestibular = @(int)$_GET['id_fonte_vestibular'];
    
    //====================================================================================================================//
    //======================================== Valida Acesso do Usuário na Página ========================================//
    //====================================================================================================================//
?>
<!DOCTYPE html>
<html>
    <head>
        <title>ADM Interbits | Avaliar Questões</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <script type="text/javascript" src="../js/libs/jquery_171.js"></script>
        
        <script type="text/javascript">
            function selecionaFiltro(filtro){
                $("input[name=tipo_filtro]").each(function(){
                    $("#" + this.value).css("background-color", "#DADADA");
                    $("#" + this.value).attr("disabled", "disabled");
                    $("#" + this.value).val(0);
                });
                
                $("#" + filtro).css("background-color", "#FFF");
                $("#" + filtro).removeAttr("disabled");
            }
            
            function atualizaUsuarioQuestao(id_questao, id_usuario){
                if(confirm("Tem certeza que deseja alterar o usuário.\nIsso pode limpar avaliações anteriores")){
                    $.post(
                        "top10_avaliar_questoes.php",
                        {
                            id_questao: id_questao,
                            id_usuario: id_usuario,
                            hdd_acao: 'usuario_questao'
                        },
                        function(ret){
                            alert(ret.msg);
                        },
                        'json'
                    );
                }else{
                    return false;
                }
            }
        </script>
    </head>
    <body>
        <h1>Questões a serem avaliadas</h1>
        <?
            if($usuario->getIdPerfil() == 1){
                $materia    = new Materia();
                $materias   = $materia->listaMaterias();
                
                $fonte  = new Fonte();
                $fontes = $fonte->listaFontes();
        ?>
        <form id="form_filtros" name="form_filtros" method="get" action="top10_avaliar_questoes.php">
            <table>
                <tr>
                    <td colspan="3">
                        Filtrar por:
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="radio" name="tipo_filtro" value="id_materia" checked="checked" onclick="javascript:selecionaFiltro(this.value);" />
                    </td>
                    <td>
                        Matérias
                    </td>
                    <td>
                        <select id="id_materia" name="id_materia">
                            <option value="0">Selecione uma matéria</option>
                            <? foreach ($materias as $row) { ?>
                            <option value="<?=$row->getIdMateria()?>"><?=utf8_encode($row->getMateria())?></option>c
                            <? } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="radio" name="tipo_filtro" value="id_fonte_vestibular" onclick="javascript:selecionaFiltro(this.value);" />
                    </td>
                    <td>
                        Fontes
                    </td>
                    <td>
                        <select id="id_fonte_vestibular" name="id_fonte_vestibular" style="background-color:#DADADA;" disabled="disabled">
                            <option value="0">Selecione uma fonte</option>
                            <? foreach ($fontes as $row) { ?>
                            <option value="<?=$row->getIdFonteVestibular()?>"><?=utf8_encode($row->getFonteVestibular())?></option>c
                            <? } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <input type="submit" value="Filtrar" />
                    </td>
                </tr>
            </table>
        </form>
        <?
            }
        ?>
        <? 
            if($usuario->getIdPerfil() == 1){
                $rs = $questoes->listaQuestoesTop10Materia($id_materia, $id_fonte_vestibular);
            }else{
                $rs = $questoes->listaQuestoesTop10Colaborador($usuario->getIdUsuario());
            }

            if(sizeof($rs) <= 0){
                $rs = $questoes->listaQuestoesTop10Materia(0, 0);
            }

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
                    Fonte
                </th>
                <th>
                    Matéria(s)
                </th>
                <th>
                    Total / Uso
                </th>
                <? if($usuario->getIdPerfil() == 1){ ?>
                <th>
                    Usuário para Avaliar
                </th>
                <? } ?>
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
                    <?=$questao['questao']->getIdBcoQuestao()?>
                </td>
                <td>
                    <?=$questao['questao']->getFonteVestibular()?>
                </td>
                <td>
                    <?=utf8_encode($questao['questao']->getMaterias())?>
                </td>
                <td>
                    <?=$questao['questao']->getTotalUso()?>
                </td>
                <? if($usuario->getIdPerfil() == 1){ ?>
                <td>
                    <select id="id_usuario" name="id_usuario" onchange="javascript:atualizaUsuarioQuestao(<?=$questao['questao']->getIdBcoQuestao()?>, this.value);">
                        <option value="0">Selecione um usuário</option>
                        <? foreach($questao['usuarios'] as $row_usuario){ ?>
                        <option value="<?=$row_usuario->ID_USUARIO?>"><?=$row_usuario->NOME?></option>
                        <? } ?>
                    </select>
                </td>
                <? } ?>
                <td>
                    <? if($questao['questao']->ID_AVALIACAO_QUESTAO > 0){ ?>
                        <a href="top10_avaliar_questao.php?id_questao=<?=$questao['questao']->getIdBcoQuestao()?>" target="_blank">
                            <img src="../img/avaliacao-concluida.jpg" border="0" style="width:23px;height:23px;" />
                        </a>
                    <? }else{ ?>
                        <a href="top10_avaliar_questao.php?id_questao=<?=$questao['questao']->getIdBcoQuestao()?>" target="_blank">
                            <img src="../img/icone_avaliar.gif" border="0" style="width:23px;height:23px;" />
                        </a>
                    <? } ?>
                </td>
            </tr>
            <? 
                    $pos++; 
                } 
            ?>
        </table>
    </body>
</html>
