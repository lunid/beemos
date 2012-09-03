<?
    //====================================================================================================================//
    //======================================== Valida Acesso do Usuário na Página ========================================//
    //====================================================================================================================//

    session_start();
    
    require_once 'class/usuario.php';
    require_once 'class/questoes.php';
    require_once 'class/avaliacao_questao.php';
    
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
    
    //Variável para conttrole.
    $salvar = false;
    
    if($_POST){
        $avaliacao = new AvaliacaoQuestao();
        $avaliacao->setIdBcoQuestao   ($_POST['id_questao']);
        $avaliacao->setIdUsuario      ($usuario->getIdUsuario());
        
        $avaliacao->setNotaEnunciado($_POST['nota_enunciado']);
        $avaliacao->setNotaAbrangencia($_POST['nota_abrangencia']);
        $avaliacao->setNotaIlustracao($_POST['nota_ilustracao']);
        $avaliacao->setNotaInterdisciplinaridade($_POST['nota_interdisciplinaridade']);
        $avaliacao->setNotaHabilidadeCompetencia($_POST['nota_habilidadecompetencia']);
        $avaliacao->setNotaOriginalidade($_POST['nota_originalidade']);
        
        $salvar = $avaliacao->salvaAvaliacaoQuestao();
    }
    
    //Inicia o valor de id_materia enviado via GET
    $id_questao = @(int)$_GET['id_questao'];
    
    //Inicia a instância do objeto Questoes
    $questao = new Questoes($id_questao);
    
    //====================================================================================================================//
    //======================================== Valida Acesso do Usuário na Página ========================================//
    //====================================================================================================================//
?>
<!DOCTYPE html>
<html>
    <head>
        <title>ADM Interbits | TOP 10 | Avaliar Questão</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <script type="text/javascript" src="../js/libs/jquery_171.js"></script>
        
        <link rel="stylesheet" type="text/css" media="screen" href="../js/libs/jqgrid/themes/redmond/jquery-ui-custom.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="../js/libs/jqgrid/themes/ui.jqgrid.css" />

        <script src="../js/libs/jqgrid/js/i18n/grid.locale-pt-br.js" type="text/javascript"></script>
        <script src="../js/libs/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
        
        <script type="text/javascript" src="../js/libs/util/dictionary.js"></script>
        <script type="text/javascript" src="../js/libs/util/form.js"></script>
        
        <script type="text/javascript">
            function selecionaNota(id){
                var dados = id.split("_");
                
                for(var i=1; i<=5; i++){
                    $("#" + dados[0] + "_" + i).attr("src", "../img/estrela_vazia.jpg");
                }
                
                for(var i=1; i<=dados[1]; i++){
                    $("#" + dados[0] + "_" + i).attr("src", "../img/estrela_cheia.jpg");
                }
                
                $("#nota_" + dados[0]).val(dados[1]);
                
                return false;
            }
        </script>
    </head>
    <body>
        <h1>Questão</h1>
        <?
            if($salvar){
        ?>
        <div id="msg">
            Avaliação concluída com sucesso.
        </div>
        <?
            }
        ?>
        <? 
            if($questao->getIdBcoQuestao() > 0){
        ?>
        <br />
        <div id="enunciado">
            <fieldset>
                <legend>Enunciado</legend>
                <img src="../img/questoes/enunciado.gif" />
            </fieldset>
        </div>
        <div id="resolucao">
            <fieldset>
                <legend>Resolução</legend>
                <img src="../img/questoes/resolucao.gif" />
            </fieldset>
        </div>
        <div id="avaliacao">
            <form id="form_avaliacao" method="post" action="top10_avaliar_questao.php?id_questao=<?=$id_questao?>" onsubmit="if(confirm('Tem certeza que deseja finaliza a Avaliação?')){ return form.validate(this); }else{ return false; }">
                <table>
                    <tr>
                        <td>
                            Enunciado:
                        </td>
                        <td>
                            <img id="enunciado_1" name="enunciado_1" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="enunciado_2" name="enunciado_2" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="enunciado_3" name="enunciado_3" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="enunciado_4" name="enunciado_4" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="enunciado_5" name="enunciado_5" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            
                            <input type="hidden" id="nota_enunciado" name="nota_enunciado" class="required" value="0" field_name="Nota de Enunciado" />
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            Abrangência:
                        </td>
                        <td>
                            <img id="abrangencia_1" name="abrangencia_1" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="abrangencia_2" name="abrangencia_2" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="abrangencia_3" name="abrangencia_3" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="abrangencia_4" name="abrangencia_4" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="abrangencia_5" name="abrangencia_5" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            
                            <input type="hidden" id="nota_abrangencia" name="nota_abrangencia" class="required" value="0" field_name="Nota de Abrangência" />
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            Ilustração:
                        </td>
                        <td>
                            <img id="ilustracao_1" name="ilustracao_1" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="ilustracao_2" name="ilustracao_2" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="ilustracao_3" name="ilustracao_3" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="ilustracao_4" name="ilustracao_4" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="ilustracao_5" name="ilustracao_5" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            
                            <input type="hidden" id="nota_ilustracao" name="nota_ilustracao" class="required" value="0" field_name="Nota de Ilustração" />
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            Interdisciplinaridade:
                        </td>
                        <td>
                            <img id="interdisciplinaridade_1" name="interdisciplinaridade_1" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="interdisciplinaridade_2" name="interdisciplinaridade_2" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="interdisciplinaridade_3" name="interdisciplinaridade_3" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="interdisciplinaridade_4" name="interdisciplinaridade_4" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="interdisciplinaridade_5" name="interdisciplinaridade_5" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            
                            <input type="hidden" id="nota_interdisciplinaridade" name="nota_interdisciplinaridade" class="required" value="0" field_name="Nota de Interdisciplinaridade" />
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            Habilidades e Competências:
                        </td>
                        <td>
                            <img id="habilidadecompetencia_1" name="habilidadecompetencia_1" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="habilidadecompetencia_2" name="habilidadecompetencia_2" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="habilidadecompetencia_3" name="habilidadecompetencia_3" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="habilidadecompetencia_4" name="habilidadecompetencia_4" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="habilidadecompetencia_5" name="habilidadecompetencia_5" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            
                            <input type="hidden" id="nota_habilidadecompetencia" name="nota_habilidadecompetencia" class="required" value="0" field_name="Nota de Habilidades e Competências" />
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            Originalidade:
                        </td>
                        <td>
                            <img id="originalidade_1" name="originalidade_1" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="originalidade_2" name="originalidade_2" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="originalidade_3" name="originalidade_3" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="originalidade_4" name="originalidade_4" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            &nbsp;
                            <img id="originalidade_5" name="originalidade_5" src="../img/estrela_vazia.jpg" style="width:25px;height:25px;cursor:pointer;" onclick="javascript:selecionaNota(this.id);" />
                            
                            <input type="hidden" id="nota_originalidade" name="nota_originalidade" class="required" value="0" field_name="Nota de Originalidade" />
                        </td>
                    </tr>
                    
                    <? if($questao->validaQuestaoAvaliacao() && $usuario->validaUsuarioAvaliacao($id_questao)){ ?>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" id="id_questao" name="id_questao" value="<?=$id_questao?>" />
                            <input type="submit" value="Salvar Avaliação" />
                        </td>
                    </tr>
                    <? } ?>
                </table>
            </form>
        </div>
        <?
            }else{
        ?>
        <h1>Nenhuma Questão encontrada.</h1>
        <?
            }
        ?>
        <script type="text/javascript">
            $(document).ready(function() {
                $.post(
                    "../dic/pt/javascript.xml",
                    null,
                    function(xml){
                        xml_dic = xml;
                        form    = new Form();
                        form.init('form_avaliacao');
                        
                        <? 
                            $avaliacao = $questao->getAvaliacaoQuestao();
                            if($avaliacao != null){
                                if($avaliacao->getNotaEnunciado() > 0){
                        ?>
                                    selecionaNota("enunciado_" + <?=$avaliacao->getNotaEnunciado()?>);
                        <? 
                                }
                                
                                if($avaliacao->getNotaAbrangencia() > 0){
                        ?>
                                    selecionaNota("abrangencia_" + <?=$avaliacao->getNotaAbrangencia()?>);
                        <?
                                }
                                
                                if($avaliacao->getNotaIlustracao() > 0){
                        ?>
                                    selecionaNota("ilustracao_" + <?=$avaliacao->getNotaIlustracao()?>);
                        <?
                                }
                                
                                if($avaliacao->getNotaInterdisciplinaridade() > 0){
                        ?>
                                    selecionaNota("interdisciplinaridade_" + <?=$avaliacao->getNotaInterdisciplinaridade()?>);
                        <?
                                }
                                
                                if($avaliacao->getNotaHabilidadeCompetencia() > 0){
                        ?>
                                    selecionaNota("habilidadecompetencia_" + <?=$avaliacao->getNotaHabilidadeCompetencia()?>);
                        <?
                                }
                                
                                if($avaliacao->getNotaOriginalidade() > 0){
                        ?>
                                    selecionaNota("originalidade_" + <?=$avaliacao->getNotaOriginalidade()?>);
                        <?
                                }
                            }
                        ?>
                    },
                    'xml'
                );                
            });
        </script>
    </body>
</html>
