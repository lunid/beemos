<?
    require_once 'class/questao.php';
    
    $id_questao = 118581;
    $questao    = new Questao($id_questao);
    $class      = $questao->carregaClassificacao();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Módulo de reclassificação</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        
        <script type="text/javascript" src="js/jquery.js"></script>
        
        <script type="text/javascript">
            function montaCombo(tipo, classificacao){
                try{
                    switch(tipo){
                        case 'id_materia':
                            $("#id_divisao_" + classificacao).css("display", "none");
                            $("#id_topico_" + classificacao).css("display", "none");
                            $("#id_item_" + classificacao).css("display", "none");
                            $("#id_subitem_" + classificacao).css("display", "none");
                            
                            $("#id_materia_" + classificacao).css("display", "");
                            $("#id_materia_" + classificacao).html("Aguarde...");
                            break;
                        case 'id_divisao':
                            $("#id_topico_" + classificacao).css("display", "none");
                            $("#id_item_" + classificacao).css("display", "none");
                            $("#id_subitem_" + classificacao).css("display", "none");
                            
                            $("#id_divisao_" + classificacao).css("display", "");
                            $("#id_divisao_" + classificacao).html(" >> Aguarde...");
                            break;
                        case 'id_topico':
                            $("#id_item_" + classificacao).css("display", "none");
                            $("#id_subitem_" + classificacao).css("display", "none");
                            
                            $("#id_topico_" + classificacao).css("display", "");
                            $("#id_topico_" + classificacao).html(" >> Aguarde...");
                            break;
                        case 'id_item':
                            $("#id_subitem_" + classificacao).css("display", "none");
                            
                            $("#id_item_" + classificacao).css("display", "");
                            $("#id_item_" + classificacao).html(" >> Aguarde...");
                            break;
                        case 'id_subitem':
                            $("#id_subitem_" + classificacao).css("display", "");
                            $("#id_subitem_" + classificacao).html(" >> Aguarde...");
                            break;
                    }
                    
                    $.post(
                        'ajax/monta_combo.php',
                        {
                            tipo: tipo,
                            valor: $("#" + tipo + "_" + classificacao).attr("value"),
                            classificacao: classificacao,
                            id_materia: $("#sel_id_materia_" + classificacao).val() > 0 ? $("#sel_id_materia_" + classificacao).val() : $("#id_materia_or_" + classificacao).val(),
                            id_divisao: $("#sel_id_divisao_" + classificacao).val() > 0 ? $("#sel_id_divisao_" + classificacao).val() : $("#id_divisao_or_" + classificacao).val(),
                            id_topico: $("#sel_id_topico_" + classificacao).val() > 0 ? $("#sel_id_topico_" + classificacao).val() : $("#id_topico_or_" + classificacao).val(),
                            id_item: $("#sel_id_item_" + classificacao).val() > 0 ? $("#sel_id_item_" + classificacao).val() : $("#id_item_or_" + classificacao).val(),
                            id_subitem: $("#sel_id_subitem_" + classificacao).val() > 0 ? $("#sel_id_subitem_" + classificacao).val() : $("#id_subitem_or_" + classificacao).val()
                        },
                        function(html){
                            var seta = "";
                            
                            if(tipo != "id_materia"){
                                seta = " >> ";
                            }
                            
                            $("#" + tipo + "_" + classificacao).html(seta + html);
                            $("#bt_salvar_" + classificacao).css("display", "");
                            $("#bt_cancelar_" + classificacao).css("display", "");
                        },
                        'html'
                    );
                }catch(err){
                    alert(err.message);
                }
            }
            
            function cancela(classificacao){
                try{
                    $("#id_materia_" + classificacao).css("display", "");
                    if($("#id_materia_or_" + classificacao).attr("name") != ''){
                        $("#id_materia_" + classificacao).html("<a href=\"javascript:void(0);\" onclick=\"montaCombo('id_materia', '" + classificacao + "');\">" + $("#id_materia_or_" + classificacao).attr("name") + "</a>");
                    }else{
                        $("#id_materia_" + classificacao).html("");
                    }
                    
                    $("#id_divisao_" + classificacao).css("display", "");
                    if($("#id_divisao_or_" + classificacao).attr("name") != ''){
                        $("#id_divisao_" + classificacao).html(" >> <a href=\"javascript:void(0);\" onclick=\"montaCombo('id_divisao', '" + classificacao + "');\">" + $("#id_divisao_or_" + classificacao).attr("name") + "</a>");
                    }else{
                        $("#id_divisao_" + classificacao).html("");
                    }
                    
                    $("#id_topico_" + classificacao).css("display", "");
                    if($("#id_topico_or_" + classificacao).attr("name") != ''){
                        $("#id_topico_" + classificacao).html(" >> <a href=\"javascript:void(0);\" onclick=\"montaCombo('id_topico', '" + classificacao + "');\">" + $("#id_topico_or_" + classificacao).attr("name") + "</a>");
                    }else{
                        $("#id_topico_" + classificacao).html("");
                    }
                    
                    $("#id_item_" + classificacao).css("display", "");
                    if($("#id_item_or_" + classificacao).attr("name") != ''){
                        $("#id_item_" + classificacao).html(" >> <a href=\"javascript:void(0);\" onclick=\"montaCombo('id_item', '" + classificacao + "');\">" + $("#id_item_or_" + classificacao).attr("name") + "</a>");
                    }else{
                        $("#id_item_" + classificacao).html("");
                    }
                    
                    $("#id_subitem_" + classificacao).css("display", "");
                    if($("#id_subitem_or_" + classificacao).attr("name") != ''){
                        $("#id_subitem_" + classificacao).html(" >> <a href=\"javascript:void(0);\" onclick=\"montaCombo('id_subitem', '" + classificacao + "');\">" + $("#id_subitem_or_" + classificacao).attr("name") + "</a>");
                    }else{
                        $("#id_subitem_" + classificacao).html("");
                    }
                    
                    $("#bt_salvar_" + classificacao).css("display", "none");
                    $("#bt_cancelar_" + classificacao).css("display", "none");
                }catch(err){
                    alert(err.message);
                }
            }
            
            function salvaClassificacao(id, id_questao, classificacao){
                try{
                    if(id > 0 && id_questao > 0){
                        if(confirm("Tem certeza que deseja salvar essa alteração de Classificação?")){
                            $.post(
                                'ajax/alteraClassificacao.php',
                                {
                                    id_classificacao: id,
                                    id_questao: id_questao,
                                    id_materia: $("#sel_id_materia_" + classificacao).val() > 0 ? $("#sel_id_materia_" + classificacao).val() : 0,
                                    id_divisao: $("#sel_id_divisao_" + classificacao).val() > 0 ? $("#sel_id_divisao_" + classificacao).val() : 0,
                                    id_topico: $("#sel_id_topico_" + classificacao).val() > 0 ? $("#sel_id_topico_" + classificacao).val() : 0,
                                    id_item: $("#sel_id_item_" + classificacao).val() > 0 ? $("#sel_id_item_" + classificacao).val() : 0,
                                    id_subitem: $("#sel_id_subitem_" + classificacao).val() > 0 ? $("#sel_id_subitem_" + classificacao).val() : 0,
                                    id_materia_or: $("#id_materia_or_" + classificacao).val(),
                                    id_divisao_or: $("#id_divisao_or_" + classificacao).val(),
                                    id_topico_or: $("#id_topico_or_" + classificacao).val(),
                                    id_item_or: $("#id_item_or_" + classificacao).val(),
                                    id_subitem_or: $("#id_subitem_or_" + classificacao).val()
                                },
                                function(ret){
                                    if(ret.status == 1){
                                        if(ret.id_materia > 0){
                                            $("#id_materia_or_" + classificacao).attr("name", $("#sel_id_materia_" + classificacao + " :selected").text() != '' && $("#sel_id_materia_" + classificacao).val() > 0 ? $("#sel_id_materia_" + classificacao + " :selected").text() : $("#id_materia_or_" + classificacao).attr("name"));
                                            $("#id_materia_or_" + classificacao).val($("#sel_id_materia_" + classificacao).val() > 0 ? $("#sel_id_materia_" + classificacao).val() : $("#id_materia_or_" + classificacao).val());
                                        }else{
                                            $("#id_materia_or_" + classificacao).attr("name", "");
                                            $("#id_materia_or_" + classificacao).val("");
                                        }
                                        
                                        if(ret.id_divisao > 0){
                                            $("#id_divisao_or_" + classificacao).attr("name", $("#sel_id_divisao_" + classificacao + " :selected").text() != '' && $("#sel_id_divisao_" + classificacao).val() > 0 ? $("#sel_id_divisao_" + classificacao + " :selected").text() : $("#id_divisao_or_" + classificacao).attr("name"));
                                            $("#id_divisao_or_" + classificacao).val($("#sel_id_divisao_" + classificacao).val() > 0 ? $("#sel_id_divisao_" + classificacao).val() : $("#id_divisao_or_" + classificacao).val());
                                        }else{
                                            $("#id_divisao_or_" + classificacao).attr("name", "");
                                            $("#id_divisao_or_" + classificacao).val("");
                                        }
                                        
                                        if(ret.id_topico > 0){
                                            $("#id_topico_or_" + classificacao).attr("name", $("#sel_id_topico_" + classificacao + " :selected").text() != '' && $("#sel_id_topico_" + classificacao).val() > 0 ? $("#sel_id_topico_" + classificacao + " :selected").text() : $("#id_topico_or_" + classificacao).attr("name"));
                                            $("#id_topico_or_" + classificacao).val($("#sel_id_topico_" + classificacao).val() > 0 ? $("#sel_id_topico_" + classificacao).val() : $("#id_topico_or_" + classificacao).val());
                                        }else{
                                            $("#id_topico_or_" + classificacao).attr("name", "");
                                            $("#id_topico_or_" + classificacao).val("");
                                        }
                                        
                                        if(ret.id_item > 0){
                                            $("#id_item_or_" + classificacao).attr("name", $("#sel_id_item_" + classificacao + " :selected").text() != '' && $("#sel_id_item_" + classificacao).val() > 0 ? $("#sel_id_item_" + classificacao + " :selected").text() : $("#id_item_or_" + classificacao).attr("name"));
                                            $("#id_item_or_" + classificacao).val($("#sel_id_item_" + classificacao).val() > 0 ? $("#sel_id_item_" + classificacao).val() : $("#id_item_or_" + classificacao).val());
                                        }else{
                                            $("#id_item_or_" + classificacao).attr("name", "");
                                            $("#id_item_or_" + classificacao).val("");
                                        }
                                        
                                        if(ret.id_subitem > 0){
                                            $("#id_subitem_or_" + classificacao).attr("name", $("#sel_id_subitem_" + classificacao + " :selected").text() != '' && $("#sel_id_subitem_" + classificacao).val() > 0 ? $("#sel_id_subitem_" + classificacao + " :selected").text() : $("#id_subitem_or_" + classificacao).attr("name"));
                                            $("#id_subitem_or_" + classificacao).val($("#sel_id_subitem_" + classificacao).val() > 0 ? $("#sel_id_subitem_" + classificacao).val() : $("#id_subitem_or_" + classificacao).val());
                                        }else{
                                            $("#id_subitem_or_" + classificacao).attr("name", "");
                                            $("#id_subitem_or_" + classificacao).val("");
                                        }
                                        
                                        cancela(classificacao);
                                    }
                                    
                                    alert(ret.msg);
                                },
                                'json'
                            );
                        }
                    }else{
                        alert("Código de Classificação e/ou Questão não encontrado(s)!");
                    }
                }catch(err){
                    alert(err.message);
                }
            }
            
            function excluiClassificacao(id, id_questao, classificacao){
                try{
                    if(id > 0 && id_questao > 0){
                        if(confirm("Tem certeza que deseja excluir essa Classificação?")){
                            $.post(
                                'ajax/excluiClassificacao.php',
                                {
                                    id_classificacao: id,
                                    id_questao: id_questao
                                },
                                function(ret){
                                    if(ret.status == 1){
                                        $("#classificacao_" + classificacao).remove();
                                    }
                                    
                                    alert(ret.msg);
                                },
                                'json'
                            );
                        }
                    }else{
                        alert("Código de Classificação e/ou Questão não encontrado(s)!");
                    }
                }catch(err){
                    alert(err.message);
                }
            }
            
            function adicionarClassificacao(id_questao){
                try{
                    var count = $("#count").val();
                    
                    $("#nova_classificacao").append("<div id='classificacao_" + count + "'>" + 
                        "<span id='id_materia_" + count + "'></span>" +
                        "<span id='id_divisao_" + count + "'></span>" +
                        "<span id='id_topico_" + count + "'></span>" +
                        "<span id='id_item_" + count + "'></span>" + 
                        "<span id='id_subitem_" + count + "'></span>" +
                        "<input type='hidden' id='id_materia_or_" + count + "' name='' value='' />" +
                        "<input type='hidden' id='id_divisao_or_" + count + "' name='' value='' />" + 
                        "<input type='hidden' id='id_topico_or_" + count + "' name='' value='' />" + 
                        "<input type='hidden' id='id_item_or_" + count + "' name='' value='' />" + 
                        "<input type='hidden' id='id_subitem_or_" + count + "' name='' value='' />" + 
                        "<input type='hidden' id='id_questao_" + count + "' value='" + id_questao + "' />" + 
                        "<input type='button' id='bt_adicionar_" + count + "' value='Salvar' onclick='javascript:salvaNovaClassificacao(" + count + ", " + id_questao + ");' />" + 
                        "<input type='button' id='bt_remover_" + count + "' value='Cancelar' onclick='javascript:removeClassificacao(" + count + ");' />" + 
                        "</div>"
                    );
                    
                    montaCombo('id_materia', count);
                    
                    count++;
                    
                    $("#count").val(count);
                }catch(err){
                    alert(err.message);
                }
            }
            
            function removeClassificacao(classificacao){
                try{
                    $("#classificacao_" + classificacao).remove();
                }catch(err){
                    alert(err.message);
                }
            }
            
            function salvaNovaClassificacao(classificacao, id_questao){
                try{
                    if(classificacao > 0 && id_questao > 0){
                        if(confirm("Tem certeza que deseja salvar esta nova Classificação?")){
                            $.post(
                                'ajax/adicionaClassificacao.php',
                                {
                                    id_questao: id_questao,
                                    id_materia: $("#sel_id_materia_" + classificacao).val() > 0 ? $("#sel_id_materia_" + classificacao).val() : 0,
                                    id_divisao: $("#sel_id_divisao_" + classificacao).val() > 0 ? $("#sel_id_divisao_" + classificacao).val() : 0,
                                    id_topico: $("#sel_id_topico_" + classificacao).val() > 0 ? $("#sel_id_topico_" + classificacao).val() : 0,
                                    id_item: $("#sel_id_item_" + classificacao).val() > 0 ? $("#sel_id_item_" + classificacao).val() : 0,
                                    id_subitem: $("#sel_id_subitem_" + classificacao).val() > 0 ? $("#sel_id_subitem_" + classificacao).val() : 0
                                },
                                function(ret){
                                    if(ret.status == 1){
                                        if(ret.id_materia > 0){
                                            $("#id_materia_or_" + classificacao).attr("name", $("#sel_id_materia_" + classificacao + " :selected").text() != '' && $("#sel_id_materia_" + classificacao).val() > 0 ? $("#sel_id_materia_" + classificacao + " :selected").text() : $("#id_materia_or_" + classificacao).attr("name"));
                                            $("#id_materia_or_" + classificacao).val($("#sel_id_materia_" + classificacao).val() > 0 ? $("#sel_id_materia_" + classificacao).val() : $("#id_materia_or_" + classificacao).val());
                                        }else{
                                            $("#id_materia_or_" + classificacao).attr("name", "");
                                            $("#id_materia_or_" + classificacao).val("");
                                        }
                                        
                                        if(ret.id_divisao > 0){
                                            $("#id_divisao_or_" + classificacao).attr("name", $("#sel_id_divisao_" + classificacao + " :selected").text() != '' && $("#sel_id_divisao_" + classificacao).val() > 0 ? $("#sel_id_divisao_" + classificacao + " :selected").text() : $("#id_divisao_or_" + classificacao).attr("name"));
                                            $("#id_divisao_or_" + classificacao).val($("#sel_id_divisao_" + classificacao).val() > 0 ? $("#sel_id_divisao_" + classificacao).val() : $("#id_divisao_or_" + classificacao).val());
                                        }else{
                                            $("#id_divisao_or_" + classificacao).attr("name", "");
                                            $("#id_divisao_or_" + classificacao).val("");
                                        }
                                        
                                        if(ret.id_topico > 0){
                                            $("#id_topico_or_" + classificacao).attr("name", $("#sel_id_topico_" + classificacao + " :selected").text() != '' && $("#sel_id_topico_" + classificacao).val() > 0 ? $("#sel_id_topico_" + classificacao + " :selected").text() : $("#id_topico_or_" + classificacao).attr("name"));
                                            $("#id_topico_or_" + classificacao).val($("#sel_id_topico_" + classificacao).val() > 0 ? $("#sel_id_topico_" + classificacao).val() : $("#id_topico_or_" + classificacao).val());
                                        }else{
                                            $("#id_topico_or_" + classificacao).attr("name", "");
                                            $("#id_topico_or_" + classificacao).val("");
                                        }
                                        
                                        if(ret.id_item > 0){
                                            $("#id_item_or_" + classificacao).attr("name", $("#sel_id_item_" + classificacao + " :selected").text() != '' && $("#sel_id_item_" + classificacao).val() > 0 ? $("#sel_id_item_" + classificacao + " :selected").text() : $("#id_item_or_" + classificacao).attr("name"));
                                            $("#id_item_or_" + classificacao).val($("#sel_id_item_" + classificacao).val() > 0 ? $("#sel_id_item_" + classificacao).val() : $("#id_item_or_" + classificacao).val());
                                        }else{
                                            $("#id_item_or_" + classificacao).attr("name", "");
                                            $("#id_item_or_" + classificacao).val("");
                                        }
                                        
                                        if(ret.id_subitem > 0){
                                            $("#id_subitem_or_" + classificacao).attr("name", $("#sel_id_subitem_" + classificacao + " :selected").text() != '' && $("#sel_id_subitem_" + classificacao).val() > 0 ? $("#sel_id_subitem_" + classificacao + " :selected").text() : $("#id_subitem_or_" + classificacao).attr("name"));
                                            $("#id_subitem_or_" + classificacao).val($("#sel_id_subitem_" + classificacao).val() > 0 ? $("#sel_id_subitem_" + classificacao).val() : $("#id_subitem_or_" + classificacao).val());
                                        }else{
                                            $("#id_subitem_or_" + classificacao).attr("name", "");
                                            $("#id_subitem_or_" + classificacao).val("");
                                        }
                                        
                                        $("#bt_adicionar_" + classificacao).remove();
                                        $("#bt_remover_" + classificacao).remove();
                                        
                                        $("#classificacao_" + classificacao).append("<input type='button' id='bt_salvar_" + classificacao + "' value='Salvar' onclick='javascript:salvaClassificacao(" + ret.id + ", " + id_questao + ", " + classificacao + ");' style='display:none;' />" + 
                                            "<input type='button' id='bt_cancelar_" + classificacao + "' value='Cancelar' onclick='javascript:cancela(" + classificacao + ");' style='display:none;' />" + 
                                            "<input type='button' id='bt_excluir_" + classificacao + "' value='Excluir' onclick='javascript:excluiClassificacao(" + ret.id + ", " + id_questao + ", " + classificacao + ");' />");
                                        
                                        cancela(classificacao);
                                    }
                                    
                                    alert(ret.msg);
                                },
                                'json'
                            );
                        }
                    }else{
                        alert("Código de Classificação e/ou Questão não encontrado(s)!");
                    }
                }catch(err){
                    alert(err.message);
                }
            }
        </script>
    </head>
    <body>
        <?
            if(sizeof($class) > 0){
                $count = 1;
                foreach($class as $row){
        ?>
        <div id="classificacao_<?=$count?>">
            <span id='id_materia_<?=$count?>'>
                <? if($row->MATERIA != null){ ?>
                <a href="javascript:void(0);" onclick="montaCombo('id_materia', '<?=$count?>')"> 
                    <?=utf8_encode($row->MATERIA)?>
                </a>
                <? }    ?>
            </span>
            <span id='id_divisao_<?=$count?>'>
                <? if($row->ID_DIVISAO != null){ ?>
                >>
                <a href="javascript:void(0);" onclick="montaCombo('id_divisao', '<?=$count?>')"> 
                    <?=utf8_encode($row->DIVISAO)?>
                </a>
                <? }    ?>
            </span>
            <span id='id_topico_<?=$count?>'>
                <? if($row->ID_TOPICO != null){ ?>
                >>
                <a href="javascript:void(0);" onclick="montaCombo('id_topico', '<?=$count?>')"> 
                    <?=utf8_encode($row->TOPICO)?>
                </a>
                <? }    ?>
            </span>
            <span id='id_item_<?=$count?>'>
                <? if($row->ID_ITEM != null){ ?>
                >>
                <a href="javascript:void(0);" onclick="montaCombo('id_item', '<?=$count?>')"> 
                    <?=utf8_encode($row->NOME_ITEM)?>
                </a>
                <? }    ?>
            </span>
            <span id='id_subitem_<?=$count?>'>
                <? if($row->ID_SUBITEM != null){ ?>
                >>
                <a href="javascript:void(0);" onclick="montaCombo('id_subitem', '<?=$count?>')"> 
                    <?=utf8_encode($row->SUBITEM)?>
                </a>
                <? }    ?>
            </span>
            
            <input type="hidden" id="id_materia_or_<?=$count?>" name="<?=utf8_encode($row->MATERIA)?>" value="<?=utf8_encode($row->ID_MATERIA)?>" />
            <input type="hidden" id="id_divisao_or_<?=$count?>" name="<?=utf8_encode($row->DIVISAO)?>" value="<?=utf8_encode($row->ID_DIVISAO)?>" />
            <input type="hidden" id="id_topico_or_<?=$count?>" name="<?=utf8_encode($row->TOPICO)?>" value="<?=utf8_encode($row->ID_TOPICO)?>" />
            <input type="hidden" id="id_item_or_<?=$count?>" name="<?=utf8_encode($row->NOME_ITEM)?>" value="<?=utf8_encode($row->ID_ITEM)?>" />
            <input type="hidden" id="id_subitem_or_<?=$count?>" name="<?=utf8_encode($row->SUBITEM)?>" value="<?=utf8_encode($row->ID_SUBITEM)?>" />
            <input type="hidden" id="id_questao_<?=$count?>" value="<?=$id_questao?>" />
            
            <input type="button" id="bt_salvar_<?=$count?>" value="Salvar" onclick="javascript:salvaClassificacao(<?=$row->ID_CLASSIFICACAO?>, <?=$id_questao?>, <?=$count?>);" style="display:none;"/>
            <input type="button" id="bt_cancelar_<?=$count?>" value="Cancelar" onclick="javascript:cancela(<?=$count?>);" style="display:none;"/>
            <input type="button" id="bt_excluir_<?=$count?>" value="Excluir" onclick="javascript:excluiClassificacao(<?=$row->ID_CLASSIFICACAO?>, <?=$id_questao?>, <?=$count?>);"/>
        </div>
        <?
                    $count++;
                }
            }
        ?>
        <div id="nova_classificacao"></div>
        <input type="hidden" id="count" value="<?=$count?>" />
        <input type="button" id="bt_adicionar" value="Adicionar" onclick="javascript:adicionarClassificacao(<?=$id_questao?>);"/>
        
    </body>
</html>
