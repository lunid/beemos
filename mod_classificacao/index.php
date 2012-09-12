<?
    require_once 'class/classificacao.php';
    
    $id_questao = 118581;
    
    $classificacao = new Classificacao();
    $classificacao->__set("ID_BCO_QUESTAO", $id_questao);
    
    $class      = $classificacao->carregaClassificacao();
    $mudancas   = $classificacao->buscaMudancas();
    $count      = 1;
    
    $bt_memoria = false;
    if(isset($_COOKIE['memoria_questao'])){
        $bt_memoria = true;
        $values     = explode(",", $_COOKIE['memoria_questao']);
        
        $m_id_materia       = $values[0];
        $m_txt_materia      = $values[1];
        $m_id_divisao       = $values[2];
        $m_txt_divisao      = $values[3];
        $m_id_topico        = $values[4];
        $m_txt_topico       = $values[5];
        $m_id_item          = $values[6];
        $m_txt_item         = $values[7];
        $m_id_subitem       = $values[8];
        $m_txt_subitem      = $values[9];
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Módulo de reclassificação</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        
        <script type="text/javascript" src="js/jquery.js"></script>
        
        <script type="text/javascript">
            function montaCombo(tipo, classificacao, valor){
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
                            valor: valor,
                            classificacao: classificacao,
                            id_materia: $("#sel_id_materia_" + classificacao).val() > 0 ? $("#sel_id_materia_" + classificacao).val() : $("#id_materia_or_" + classificacao).val(),
                            id_divisao: $("#sel_id_divisao_" + classificacao).val() > 0 ? $("#sel_id_divisao_" + classificacao).val() : $("#id_divisao_or_" + classificacao).val(),
                            id_topico: $("#sel_id_topico_" + classificacao).val() > 0 ? $("#sel_id_topico_" + classificacao).val() : $("#id_topico_or_" + classificacao).val(),
                            id_item: $("#sel_id_item_" + classificacao).val() > 0 ? $("#sel_id_item_" + classificacao).val() : $("#id_item_or_" + classificacao).val(),
                            id_subitem: $("#sel_id_subitem_" + classificacao).val() > 0 ? $("#sel_id_subitem_" + classificacao).val() : $("#id_subitem_or_" + classificacao).val()
                        },
                        function(html){
                            var seta = "";
                            
                            if(tipo != "id_materia" && html != ""){
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
                        $("#id_materia_" + classificacao).html("<a href=\"javascript:void(0);\" onclick=\"montaCombo('id_materia', '" + classificacao + "', " + $("#id_materia_or_" + classificacao).val() + ");\">" + $("#id_materia_or_" + classificacao).attr("name") + "</a>");
                    }else{
                        $("#id_materia_" + classificacao).html("");
                    }
                    
                    $("#id_divisao_" + classificacao).css("display", "");
                    if($("#id_divisao_or_" + classificacao).attr("name") != ''){
                        $("#id_divisao_" + classificacao).html(" >> <a href=\"javascript:void(0);\" onclick=\"montaCombo('id_divisao', '" + classificacao + "', " + $("#id_divisao_or_" + classificacao).val() + ");\">" + $("#id_divisao_or_" + classificacao).attr("name") + "</a>");
                    }else{
                        $("#id_divisao_" + classificacao).html("");
                    }
                    
                    $("#id_topico_" + classificacao).css("display", "");
                    if($("#id_topico_or_" + classificacao).attr("name") != ''){
                        $("#id_topico_" + classificacao).html(" >> <a href=\"javascript:void(0);\" onclick=\"montaCombo('id_topico', '" + classificacao + "', " + $("#id_topico_or_" + classificacao).val() + ");\">" + $("#id_topico_or_" + classificacao).attr("name") + "</a>");
                    }else{
                        $("#id_topico_" + classificacao).html("");
                    }
                    
                    $("#id_item_" + classificacao).css("display", "");
                    if($("#id_item_or_" + classificacao).attr("name") != ''){
                        $("#id_item_" + classificacao).html(" >> <a href=\"javascript:void(0);\" onclick=\"montaCombo('id_item', '" + classificacao + "', " + $("#id_item_or_" + classificacao).val() + ");\">" + $("#id_item_or_" + classificacao).attr("name") + "</a>");
                    }else{
                        $("#id_item_" + classificacao).html("");
                    }
                    
                    $("#id_subitem_" + classificacao).css("display", "");
                    if($("#id_subitem_or_" + classificacao).attr("name") != ''){
                        $("#id_subitem_" + classificacao).html(" >> <a href=\"javascript:void(0);\" onclick=\"montaCombo('id_subitem', '" + classificacao + "', " + $("#id_subitem_or_" + classificacao).val() + ");\">" + $("#id_subitem_or_" + classificacao).attr("name") + "</a>");
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
                        
                            if($("#sel_id_materia_" + classificacao).val() == 0){
                                alert("Selecione uma Matéria");
                                return false;
                            }
                            
                            if($("#sel_id_divisao_" + classificacao).val() == 0){
                                alert("Selecione uma Divisão");
                                return false;
                            }
                            
                            if($("#sel_id_topico_" + classificacao).val() == 0){
                                alert("Selecione um Tópico");
                                return false;
                            }
                            
                            if($("#sel_id_item_" + classificacao).val() == 0){
                                alert("Selecione um Item");
                                return false;
                            }
                            
                            if($("#sel_id_subitem_" + classificacao).val() == 0){
                                alert("Selecione um Subitem");
                                return false;
                            }
                            
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
                                        
                                        $("#salvar_mudancas_questao").css("display", "");
                                        $("#salvar_mudancas").css("display", "");
                                    }else{
                                        alert(ret.msg);
                                    }
                                    
                                },
                                'json'
                            );
                        
                    }else{
                        alert("Código de Classificação e/ou Questão não encontrado(s)!");
                    }
                }catch(err){
                    alert(err.message);
                }
            }
            
            function excluiClassificacao(id_classificacao, id_questao, classificacao, id_autoriza_classificacao){
                try{
                    if((id_classificacao > 0 || id_autoriza_classificacao > 0) && id_questao > 0){
                        if(confirm("Tem certeza que deseja excluir essa Classificação?")){
                            $.post(
                                'ajax/excluiClassificacao.php',
                                {
                                    id_classificacao: id_classificacao,
                                    id_questao: id_questao,
                                    id_autoriza_classificacao: id_autoriza_classificacao
                                },
                                function(ret){
                                    if(ret.status == 1){
                                        $("#classificacao_" + classificacao).remove();
                                        
                                        $("#salvar_mudancas_questao").css("display", "");
                                        $("#salvar_mudancas").css("display", "");
                                    }else{
                                        alert(ret.msg);
                                    }
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
                    
                    montaCombo('id_materia', count, 0);
                    
                    count++;
                    
                    $("#count").val(count);
                }catch(err){
                    alert(err.message);
                }
            }
            
            function adicionarClassificacaoMemoria(id_questao, id_materia, txt_materia, id_divisao, txt_divisao, id_topico, txt_topico, id_item, txt_item, id_subitem, txt_subitem){
                try{
                    $.post(
                        'ajax/adicionaClassificacao.php',
                        {
                            id_questao: id_questao,
                            id_materia: id_materia,
                            id_divisao: id_divisao,
                            id_topico: id_topico,
                            id_item: id_item,
                            id_subitem: id_subitem
                        },
                        function(ret){
                            if(ret.status == 1){
                                var count = $("#count").val();
                    
                                var html = "<span id='id_materia_" + count + "'><a href=\"javascript:void(0);\" onclick=\"montaCombo('id_materia', '" + count + "', " + id_materia + ")\">" + txt_materia + "</a></span>";

                                if(id_divisao > 0){
                                    html += "<span id='id_divisao_" + count + "'> >> <a href=\"javascript:void(0);\" onclick=\"montaCombo('id_divisao', '" + count + "', " + id_divisao + ")\">" + txt_divisao + "</a></span>";
                                }else{
                                    html += "<span id='id_divisao_" + count + "'></span>";
                                }

                                if(id_topico > 0){
                                    html += "<span id='id_topico_" + count + "'> >> <a href=\"javascript:void(0);\" onclick=\"montaCombo('id_topico', '" + count + "', " + id_topico + ")\">" + txt_topico + "</a></span>";
                                }else{
                                    html += "<span id='id_topico_" + count + "'></span>";
                                }

                                if(id_item > 0){
                                    html += "<span id='id_item_" + count + "'> >> <a href=\"javascript:void(0);\" onclick=\"montaCombo('id_item', '" + count + "', " + id_item + ")\">" + txt_item + "</a></span>";
                                }else{
                                    html += "<span id='id_item_" + count + "'></span>";
                                }

                                if(id_subitem > 0){
                                    html += "<span id='id_subitem_" + count + "'> >> <a href=\"javascript:void(0);\" onclick=\"montaCombo('id_subitem', '" + count + "', " + id_subitem + ")\">" + txt_subitem + "</a></span>";
                                }else{
                                    html += "<span id='id_subitem_" + count + "'></span>";
                                }
                                
                                $("#nova_classificacao").append("<div id='classificacao_" + count + "'>" + html + 
                                    "<input type='hidden' id='id_materia_or_" + count + "' name='" + txt_materia + "' value='" + id_materia + "' />" +
                                    "<input type='hidden' id='id_divisao_or_" + count + "' name='" + txt_divisao + "' value='" + id_divisao + "' />" + 
                                    "<input type='hidden' id='id_topico_or_" + count + "' name='" + txt_topico + "' value='" + id_topico + "' />" + 
                                    "<input type='hidden' id='id_item_or_" + count + "' name='" + txt_item + "' value='" + id_item + "' />" + 
                                    "<input type='hidden' id='id_subitem_or_" + count + "' name='" + txt_subitem + "' value='" + id_subitem + "' />" + 
                                    "<input type='hidden' id='id_questao_" + count + "' value='" + id_questao + "' />" +
                                    "<input type='hidden' id='id_autoriza_classificacao_" + count + "' value='0' />" +
                                    "</div>"
                                );
                                
                                $("#classificacao_" + count).append("&nbsp;<input type='button' id='bt_salvar_" + count + "' value='Salvar' onclick='javascript:salvaClassificacao(" + ret.id + ", " + id_questao + ", " + count + ");' style='display:none;' />" + 
                                            "&nbsp;<input type='button' id='bt_cancelar_" + count + "' value='Cancelar' onclick='javascript:cancela(" + count + ");' style='display:none;' />" + 
                                            "&nbsp;<input type='button' id='bt_remover_" + count + "' value='Excluir' onclick='javascript:excluiClassificacao(0, " + id_questao + ", " + count + ", " + ret.id + ");' />");
                                
                                $("#salvar_mudancas_questao").css("display", "");
                                $("#salvar_mudancas").css("display", "");
                                
                                count++;
                    
                                $("#count").val(count);
                            }else{
                                alert(ret.msg);
                            }
                        },
                        'json'
                    );
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
                            if($("#sel_id_materia_" + classificacao).val() == 0){
                                alert("Selecione uma Matéria");
                                return false;
                            }
                            
                            if($("#sel_id_divisao_" + classificacao).val() == 0){
                                alert("Selecione uma Divisão");
                                return false;
                            }
                            
                            if($("#sel_id_topico_" + classificacao).val() == 0){
                                alert("Selecione um Tópico");
                                return false;
                            }
                            
                            if($("#sel_id_item_" + classificacao).val() == 0){
                                alert("Selecione um Item");
                                return false;
                            }
                            
                            if($("#sel_id_subitem_" + classificacao).val() == 0){
                                alert("Selecione um Subitem");
                                return false;
                            }
                            
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
                                        
                                        $("#classificacao_" + classificacao).append("&nbsp;<input type='radio' name='memo' id='memo_" + count + "' value='" + count + "' onclick=\"javascript:memorizar(" + ret.id_materia + ", '" + $("#sel_id_materia_" + classificacao + " :selected").text() + "', " + ret.id_divisao + ", '" + $("#sel_id_divisao_" + classificacao + " :selected").text() + "', " + ret.id_topico + ", '" + $("#sel_id_topico_" + classificacao + " :selected").text() + "', " + ret.id_item + ", '" + $("#sel_id_item_" + classificacao + " :selected").text() + "', " + ret.id_subitem + ", '" + $("#sel_id_subitem_" + classificacao + " :selected").text() + "');\" /> Memorizar&nbsp;" + 
                                            "<input type='button' id='bt_salvar_" + classificacao + "' value='Salvar' onclick='javascript:salvaClassificacao(" + ret.id + ", " + id_questao + ", " + classificacao + ");' style='display:none;' />" + 
                                            "<input type='button' id='bt_cancelar_" + classificacao + "' value='Cancelar' onclick='javascript:cancela(" + classificacao + ");' style='display:none;' />" + 
                                            "<input type='button' id='bt_excluir_" + classificacao + "' value='Excluir' onclick='javascript:excluiClassificacao(0, " + id_questao + ", " + classificacao + ", " + ret.id + ");' />");
                                        
                                        cancela(classificacao);
                                        
                                        $("#salvar_mudancas_questao").css("display", "");
                                        $("#salvar_mudancas").css("display", "");
                                    }else{
                                        alert(ret.msg);
                                    }
                                },
                                'json'
                            );
                    }else{
                        alert("Código de Classificação e/ou Questão não encontrado(s)!");
                    }
                }catch(err){
                    alert(err.message);
                }
            }
            
            function memorizar(id_materia, txt_materia, id_divisao, txt_divisao, id_topico, txt_topico, id_item, txt_item, id_subitem, txt_subitem){
                try{
                    $.post(
                            'ajax/salvaMemoria.php',
                            {
                                id_materia: id_materia,
                                txt_materia: txt_materia,
                                id_divisao: id_divisao,
                                txt_divisao: txt_divisao,
                                id_topico: id_topico,
                                txt_topico: txt_topico,
                                id_item: id_item,
                                txt_item: txt_item,
                                id_subitem: id_subitem,
                                txt_subitem: txt_subitem
                            },
                            function(ret){
                                
                            },
                            'json'
                    );
                }catch(err){
                    alert(err.message);
                }
            }
            
            function desfazerMudanca(id_classificacao, id_questao, classificacao){
                try{
                    $.post(
                            'ajax/desfazerClassificacao.php',
                            {
                                id_questao: id_questao,
                                id_classificacao: id_classificacao
                            },
                            function(ret){
                                if(ret.status == 1){
                                    $("#mudanca_" + classificacao).remove();
                                    $("#label_mudanca_" + classificacao).remove();
                                    $("#classificacao_" + classificacao).css("float", "");
                                    $("#bt_excluir_" + classificacao).css("display", "");
                                    
                                    cancela(classificacao);
                                    
                                    $("#salvar_mudancas_questao").css("display", "");
                                    $("#salvar_mudancas").css("display", "");
                                }else{
                                    alert(ret.msg);
                                }
                            },
                            'json'
                          );
                }catch(err){
                    alert(err.message);
                }
            }
            
            function desfazerInsercao(id_questao, classificacao){
                try{
                    $.post(
                            'ajax/desfazerInsercaoClassificacao.php',
                            {
                                id_questao: id_questao,
                                id_autoriza_classificacao: $("#id_autoriza_classificacao_" + classificacao).val()
                            },
                            function(ret){
                                if(ret.status == 1){
                                    $("#insercao_" + classificacao).remove();
                                    
                                    $("#salvar_mudancas_questao").css("display", "");
                                    $("#salvar_mudancas").css("display", "");
                                }else{
                                    alert(ret.msg);
                                }
                            },
                            'json'
                          );
                }catch(err){
                    alert(err.message);
                }
            }
            
            function salvarMudancas(id_usuario, id_questao){
                try{
                    $.post(
                            'ajax/salvarMudancas.php',
                            {
                                id_usuario: id_usuario,
                                id_questao: id_questao
                            },
                            function(ret){
                                alert(ret.msg);
                                
                                if(ret.status == 1){
                                    window.location.reload();
                                }
                            },
                            'json'
                          );
                }catch(err){
                    alert(err.message);
                }
            }
        </script>
    </head>
    <body>
        <?
            if(sizeof($class) > 0){
                foreach($class as $row){
                    $flg_alterado   = false;
                    $mudanca        = null;
        ?>
        
        <?
            if(sizeof($mudancas) > 0){
                foreach ($mudancas as $row_mudanca) {
                    if($row_mudanca->ID_CLASSIFICACAO == $row->ID_CLASSIFICACAO){   
                        $flg_alterado   = true;
                        $mudanca        = $row_mudanca;
                    }
                }
            }
        ?>
        
        <div id="classificacao_<?=$count?>" <? if($flg_alterado){ ?> style="float:left;" <? } ?> >
            <? if($flg_alterado && $mudanca->TIPO_MUDANCA == 'A'){ ?>
            <span id="label_mudanca_<?=$count?>" style="color:#FF0000;">
                Antiga classificação: 
            </span>
            <? } ?>
            <span id='id_materia_<?=$count?>'>
                <? if($row->MATERIA != null){ ?>
                
                <? if(!$flg_alterado){ ?>
                <a href="javascript:void(0);" onclick="montaCombo('id_materia', '<?=$count?>', <?=$row->ID_MATERIA?>)"> 
                    <?=utf8_encode($row->MATERIA)?>
                </a>
                <? }else{ ?>
                    <?=utf8_encode($row->MATERIA)?>
                <? } ?>
                
                <? } ?>
            </span>
            <span id='id_divisao_<?=$count?>'>
                <? if($row->ID_DIVISAO != null){ ?>
                
                >>
                <? if(!$flg_alterado){ ?>
                <a href="javascript:void(0);" onclick="montaCombo('id_divisao', '<?=$count?>', <?=$row->ID_DIVISAO?>))"> 
                    <?=utf8_encode($row->DIVISAO)?>
                </a>
                <? }else{ ?>
                    <?=utf8_encode($row->DIVISAO)?>
                <? } ?>
                
                <? } ?>
            </span>
            <span id='id_topico_<?=$count?>'>
                <? if($row->ID_TOPICO != null){ ?>
                
                >>
                <? if(!$flg_alterado){ ?>
                <a href="javascript:void(0);" onclick="montaCombo('id_topico', '<?=$count?>', <?=$row->ID_TOPICO?>)"> 
                    <?=utf8_encode($row->TOPICO)?>
                </a>
                <? }else{ ?>
                    <?=utf8_encode($row->TOPICO)?>
                <? } ?>
                
                <? } ?>
            </span>
            <span id='id_item_<?=$count?>'>
                <? if($row->ID_ITEM != null){ ?>
                
                >>
                <? if(!$flg_alterado){ ?>
                <a href="javascript:void(0);" onclick="montaCombo('id_item', '<?=$count?>', <?=$row->ID_ITEM?>)"> 
                    <?=utf8_encode($row->NOME_ITEM)?>
                </a>
                <? }else{ ?>
                    <?=utf8_encode($row->NOME_ITEM)?>
                <? } ?>
                
                <? } ?>
            </span>
            <span id='id_subitem_<?=$count?>'>
                <? if($row->ID_SUBITEM != null){ ?>
                
                <? if(!$flg_alterado){ ?>
                >>
                <a href="javascript:void(0);" onclick="montaCombo('id_subitem', '<?=$count?>', <?=$row->ID_IDSUBITEM?>)"> 
                    <?=utf8_encode($row->SUBITEM)?>
                </a>
                <? }else{ ?>
                    <?=utf8_encode($row->SUBITEM)?>
                <? } ?>
                
                <? } ?>
            </span>
            
            <input type="hidden" id="id_materia_or_<?=$count?>" name="<?=utf8_encode($row->MATERIA)?>" value="<?=utf8_encode($row->ID_MATERIA)?>" />
            <input type="hidden" id="id_divisao_or_<?=$count?>" name="<?=utf8_encode($row->DIVISAO)?>" value="<?=utf8_encode($row->ID_DIVISAO)?>" />
            <input type="hidden" id="id_topico_or_<?=$count?>" name="<?=utf8_encode($row->TOPICO)?>" value="<?=utf8_encode($row->ID_TOPICO)?>" />
            <input type="hidden" id="id_item_or_<?=$count?>" name="<?=utf8_encode($row->NOME_ITEM)?>" value="<?=utf8_encode($row->ID_ITEM)?>" />
            <input type="hidden" id="id_subitem_or_<?=$count?>" name="<?=utf8_encode($row->SUBITEM)?>" value="<?=utf8_encode($row->ID_SUBITEM)?>" />
            <input type="hidden" id="id_questao_<?=$count?>" value="<?=$id_questao?>" />
            
            
            &nbsp;<input type="button" id="bt_salvar_<?=$count?>" value="Salvar" onclick="javascript:salvaClassificacao(<?=$row->ID_CLASSIFICACAO?>, <?=$id_questao?>, <?=$count?>);" style="display:none;" />
            &nbsp;<input type="button" id="bt_cancelar_<?=$count?>" value="Cancelar" onclick="javascript:cancela(<?=$count?>);" style="display:none;" />
            &nbsp;<input type="button" id="bt_excluir_<?=$count?>" value="Excluir" onclick="javascript:excluiClassificacao(<?=$row->ID_CLASSIFICACAO?>, <?=$id_questao?>, <?=$count?>);" <? if($flg_alterado){ ?> style="display:none;" <? } ?> />
        </div>
        
        <? if($flg_alterado && $mudanca->TIPO_MUDANCA == 'A'){ ?>
        <div id="mudanca_<?=$count?>">
            <span style="color:blue;">
                &nbsp;
                Nova classificação: 
            </span>
            <span>
                <? if($mudanca->MATERIA != null){ ?> <?=utf8_encode($mudanca->MATERIA)?> <? } ?>
            </span>
            <span>
                <? if($mudanca->DIVISAO != null){ ?> >> <?=utf8_encode($mudanca->DIVISAO)?> <? } ?>
            </span>
            <span>
                <? if($mudanca->TOPICO != null){ ?> >> <?=utf8_encode($mudanca->TOPICO)?> <? } ?>
            </span>
            <span>
                <? if($mudanca->NOME_ITEM != null){ ?> >> <?=utf8_encode($mudanca->NOME_ITEM)?> <? } ?>
            </span>
            <span>
                <? if($mudanca->SUBITEM != null){ ?> >> <?=utf8_encode($mudanca->SUBITEM)?> <? } ?>
            </span>
            
            &nbsp;<input type="button" id="bt_desfazer_<?=$count?>" value="Desfazer Mudança" onclick="javascript:desfazerMudanca(<?=$row->ID_CLASSIFICACAO?>, <?=$id_questao?>, <?=$count?>);"/>
        </div>
        
        
        <? }elseif($flg_alterado && $mudanca->TIPO_MUDANCA == 'E'){ ?>
        <div id="mudanca_<?=$count?>">
            <span style="color:red;">
                &nbsp;
                Classificação excluída
            </span>
            
            &nbsp;<input type="button" id="bt_desfazer_<?=$count?>" value="Desfazer Mudança" onclick="javascript:desfazerMudanca(<?=$row->ID_CLASSIFICACAO?>, <?=$id_questao?>, <?=$count?>);"/>
        </div>
        <? } ?>
        
        <br />
        <?
                    $count++;
                }
            }
        ?>
        
        <?
            if(sizeof($mudancas) > 0){
                foreach ($mudancas as $row_mudanca) {
                    if($row_mudanca->ID_BCO_QUESTAO == $id_questao && $row_mudanca->TIPO_MUDANCA == 'I'){   
        ?>
        <div id="insercao_<?=$count?>">
            <span style="color:green;">
                Classificação adicionada: 
            </span>
            <span>
                <? if($row_mudanca->MATERIA != null){ ?> <?=utf8_encode($row_mudanca->MATERIA)?> <? } ?>
            </span>
            <span>
                <? if($row_mudanca->DIVISAO != null){ ?> >> <?=utf8_encode($row_mudanca->DIVISAO)?> <? } ?>
            </span>
            <span>
                <? if($row_mudanca->TOPICO != null){ ?> >> <?=utf8_encode($row_mudanca->TOPICO)?> <? } ?>
            </span>
            <span>
                <? if($row_mudanca->NOME_ITEM != null){ ?> >> <?=utf8_encode($row_mudanca->NOME_ITEM)?> <? } ?>
            </span>
            <span>
                <? if($row_mudanca->SUBITEM != null){ ?> >> <?=utf8_encode($row_mudanca->SUBITEM)?> <? } ?>
            </span>
            
            <input type="hidden" id="id_autoriza_classificacao_<?=$count?>" value="<?=utf8_encode($row_mudanca->ID_AUTORIZA_CLASSIFICACAO)?>" />
            
            &nbsp;<input type="button" id="bt_desfazer_ins_<?=$count?>" value="Desfazer Mudança" onclick="javascript:desfazerInsercao(<?=$id_questao?>, <?=$count?>);"/>
        </div>                
        <?
                    }
                }
            }
        ?>
        
        
        <div id="nova_classificacao"></div>
        <input type="hidden" id="count" value="<?=$count?>" />
        <input type="button" id="bt_adicionar" value="Adicionar" onclick="javascript:adicionarClassificacao(<?=$id_questao?>);"/>
        <? if($bt_memoria){ ?><input type="button" id="bt_adicionar_memoria" value="Adicionar Memo" onclick="javascript:adicionarClassificacaoMemoria(<?=$id_questao?>, <?=$m_id_materia?>, '<?=$m_txt_materia?>', <?=$m_id_divisao?>, '<?=$m_txt_divisao?>', <?=$m_id_topico?>, '<?=$m_txt_topico?>', <?=$m_id_item?>, '<?=$m_txt_item?>', <?=$m_id_subitem?>, '<?=$m_txt_subitem?>');"/><? } ?>
        <br /><br /><br /><br /><br />
        <input type="button" id="salvar_mudancas_questao" value="Salvar Todas Mudanças (Questao)" onclick="javascript:salvarMudancas(1, <?=$id_questao?>);" <? if(sizeof($mudancas) <= 0){ ?> style="display:none" <? } ?> />
        <input type="button" id="salvar_mudancas" value="Salvar Todas Mudanças" onclick="javascript:salvarMudancas(1);" <? if(sizeof($mudancas) <= 0){ ?> style="display:none" <? } ?> />
    </body>
</html>
