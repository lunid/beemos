$(document).ready(function(){
    //Carrega Grid de Mensagens Recebidas
    $("#grid_caixa_postal").jqGrid({
        url: 'caixapostal/gridRecebidas',
        datatype: "json",
        colNames:['', 'De', 'Assunto', 'Data', 'Status'],
        colModel:[
                //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
                {name:'BOX', index:'BOX', width:10, align:'center', search: false, cellattr: site.formataGrid, sortable: false },
                {name:'NOME_PRINCIPAL', index:'NOME_PRINCIPAL', width:70, search: true, cellattr: verificaNaoLidas},
                {name:'ASSUNTO', index:'ASSUNTO', search: true, cellattr: verificaNaoLidas},
                {name:'DT_ENVIO', index:'DT_ENVIO', width:50, align:'center', search: true, 
                    searchoptions:{
                        dataInit:function(elem){
                            $(elem).datepicker({
                                onSelect: function() {
                                    $("#grid_caixa_postal")[0].triggerToolbar();
                                }
                            });
                        }
                    }, 
                    cellattr: verificaNaoLidas},
                {name:'STATUS', index:'STATUS', hidden: true, cellattr: verificaNaoLidas}
        ],
        rowNum:10,
        rowList:[10,30,60],
        pager: '#pg_caixa_postal',
        sortname: 'DT_ENVIO',
        viewrecords: true,
        sortorder: "DESC",
        caption:"Recebidas",
        width: 750,
        height: 'auto',
        scrollOffset: 0
    });

    $("#grid_caixa_postal").filterToolbar();
    
    //Formulário de envio
    formEnviarMsg = new Form();
    formEnviarMsg.init('form_enviar_msg');
});

/**
 * Verifica mensagens que não foram lindas e troca cor do texto
 */
function verificaNaoLidas(rowId, tv, rawObject, cm, rdata) {
    if(rawObject[4] == 'nao_lida'){
        return " style='color:#2971D5;font-weight:bold;' ";
    }
}

/**
 * Abre uma mensagem recebida para exibição
 * Controla cores no grid
 */
function abreMsgRecebida(id){
    $.post(
        'caixapostal/carregarMensagem',
        {
            idCaixaMsg: id,
            tipo: 'recebida'
        },
        function(ret){
            if(ret.status){
                //Armaze HTML do link na celula DE
                var cell    = $("#grid_caixa_postal").getCell(id, 1);
                cell        = cell.replace("#2971D5", "#000");

                $("#grid_caixa_postal").setCell(id, 1, cell, {color:'#000', fontWeight:'normal'});
                $("#grid_caixa_postal").setCell(id, 2, '', {color:'#000', fontWeight:'normal'});
                $("#grid_caixa_postal").setCell(id, 3, '', {color:'#000', fontWeight:'normal'});
                
                //Prenche campos do Modal e Abre para visualização
                $("#abrir_msg").html(ret.mensagem.MSG);
                
                //Abre Modal
                $("#modal_abrir").dialog({
                    title: ret.mensagem.ASSUNTO,
                    modal: true,
                    width: 800,
                    position: [null, 50]
                });
            }else{
                alert(ret.msg);
            }
        },
        'json'
    ).error(function(){
        alert("Falha na requisição ao SERVIDOR! Entre em contato com o Suporte.");
    });
}

/**
 * Abre uma mensagem enviada para exibição
 */
function abreMsgEnviada(id){
    $.post(
        'caixapostal/carregarMensagem',
        {
            idCaixaMsg: id,
            tipo: 'enviada'
        },
        function(ret){
            if(ret.status){
                //Prenche campos do Modal e Abre para visualização
                $("#abrir_msg").html(ret.mensagem.MSG);
                
                //Abre Modal
                $("#modal_abrir").dialog({
                    title: ret.mensagem.ASSUNTO,
                    modal: true,
                    width: 800,
                    position: [null, 50]
                });
            }else{
                alert(ret.msg);
            }
        },
        'json'
    ).error(function(){
        alert("Falha na requisição ao SERVIDOR! Entre em contato com o Suporte.");
    });
}

/**
 * Abre caixa de dialogo para enciar mensagens
 */
function escrever(){
    $("#modal_escrever").dialog({
        title: "Escrever Mensagem",
        modal: true,
        width: 800
    });
}

/**
 * Abre dialogo com alunos do Cliente para seleção de envio Para
 */
function listarPara(){
    //Efetua chamada Ajax para montar grid de alunos
    //Inicializando o Grid de Escolas (Aba Escolas & Turmas)
    $("#grid_para").jqGrid({
        url: 'caixapostal/listarAlunosPara',
        datatype: "json",
        colNames:['', 'COD', 'Nome', 'Escola', 'Turma'],
        colModel:[
                //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
                {name:'BOX', index:'BOX', width:10, align:'center', search: false, cellattr: site.formataGrid, sortable: false },
                {name:'ID_CLIENTE', index:'ID_CLIENTE', width:25, align:'center', search: true},
                {name:'NOME_PRINCIPAL', index:'NOME_PRINCIPAL', search: true},
                {name:'ESCOLA', index:'ESCOLA', width:70, align:'center', search: true},
                {name:'CLASSE', index:'CLASSE', width:30, align:'center', search: true}
        ],
        rowNum:10,
        rowList:[10,30,60],
        pager: '#pg_para',
        sortname: 'NOME_PRINCIPAL',
        viewrecords: true,
        sortorder: "ASC",
        caption:"Alunos",
        width: 750,
        height: 'auto',
        scrollOffset: 0
    });

    $("#grid_para").filterToolbar();
        
    $("#modal_para").dialog({
        title: "Selecione os alunos",
        modal: true,
        width: 800,
        position: [50, 50]
    });
}

/**
 * Marca ou Desmarca todos alunos do Grid Para
 */
function selecionarTodosAlunos(obj){
    $(".check_aluno").each(function(){
       this.checked = obj.checked; 
    });
}

/**
 * Finaliza caixa de dialogo para seleção de alinos Para
 */
function finalizaPara(opt){
    if(opt == true){
        //Cancatena e-mails em um string separados por pont e vírgula
        var emails = "";
        $(".check_aluno").each(function(){
            //Caso o alunos esteja seleciona seu e-mail é concatenado
            if(this.checked){
                if(emails != ""){
                    emails += "; ";
                }
                emails += this.value; 
            }
        });
        
        //Se nenhum e-mail foi encontrado, é devolvido um alerta
        if(emails == ""){
            alert("Selecione no mínimo um aluno!");
        }else{
            //Senão, os e-mail são adicionados ao PARA e a caixa de dialogo finalizada
            $("#escrever_para").val(emails);
            
            $("#modal_para").dialog("close");
        }
    }else if(opt == false){
        //Se clicar em cancelar, a caixa de dialogo é finalizada
        $("#modal_para").dialog("close");
    }
    
    //Desmarca opções
    $("#selecionar_todos_alunos").removeAttr("checked");
    $(".check_aluno").removeAttr("checked");
}

/**
 * Valida retorn odo envio de mensagens e abre opção de SMS ao cliente
 */
function validaEnvio(ret, modalId){
    $("#form_enviar_msg_erros").removeClass("warning success error");
    $("#form_enviar_msg_erros_msg").html(ret.msg);
    
    if(ret.status){
        $("#form_enviar_msg_erros").addClass("success");
        $("#form_enviar_msg_erros").show();
        
        //Atualiza Grids
        $("#grid_caixa_postal").trigger("reloadGrid");
        $("#grid_caixa_saida").trigger("reloadGrid");
        
        if(ret.sms != false){
            site.modal(
                "Existe(m) " + ret.sms.length + " aluno(s) com celular cadatsrado.<br />Deseja enviar essa mensagem por SMS?<br />Isso consumirá " + (ret.sms.length*5) + " crédito(s) da sua conta.", 
                "Envia SMS", 
                null, 
                [
                    {
                        text: "Sim", 
                        click: function(){
                            //Aramazeno objeto de dialogo para uso após Ajax
                            dialogSms = this;
                            //Requisição Ajaxa
                            $.post(
                                'caixapostal/enviarSms',
                                {
                                    idCaixaMsg: ret.id,
                                    sms: ret.sms
                                },
                                function(retSms){
                                    //Exibe retorno e fecha caixa
                                    alert(retSms.msg);
                                    $(dialogSms).dialog("close");
                                },
                                'json'
                            ).error(function(){
                                alert("Falha na requisição ao SERVIDOR! Entre em contato com o Suporte.");
                            });
                        }
                    },
                    {
                        text: "Não", 
                        click: function(){
                            //Apenas cancela disparo SMS
                            $(this).dialog("close");
                        }
                    }
                ],
                400
            );
        }
    }else{
        $("#form_enviar_msg_erros").addClass("error");
        $("#form_enviar_msg_erros").show();
    }
}

/**
 * Exibe Caixa de Entrada ou Saída
 */
function exibeCaixa(tipo){
    if(tipo == 'recebidas'){
        $("#enviadas").hide();
        $("#recebidas").show();
    }else if(tipo == 'enviadas'){
        //Carrega Grid de Mensagens Enviadas
        $("#grid_caixa_saida").jqGrid({
            url: 'caixapostal/gridEnviadas',
            datatype: "json",
            colNames:['Para', 'Assunto', 'Data'],
            colModel:[
                    //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
                    {name:'NOMES_PARA', index:'NOMES_PARA', width:90, search: true, cellattr: site.formataGrid},
                    {name:'ASSUNTO', index:'ASSUNTO', search: true},
                    {name:'DT_ENVIO', index:'DT_ENVIO', width:50, align:'center', search: true, 
                        searchoptions:{
                            dataInit:function(elem){
                                $(elem).datepicker({
                                    onSelect: function() {
                                        $("#grid_caixa_saida")[0].triggerToolbar();
                                    }
                                });
                            }
                        }
                    }
            ],
            rowNum:10,
            rowList:[10,30,60],
            pager: '#pg_caixa_saida',
            sortname: 'DT_ENVIO',
            viewrecords: true,
            sortorder: "DESC",
            caption:"Enviadas",
            width: 750,
            height: 'auto',
            scrollOffset: 0
        });

        $("#grid_caixa_saida").filterToolbar();
        
        //Exibe tela
        $("#recebidas").hide();
        $("#enviadas").show();
    }
}

/**
 * Marca e Desmarca todas as msgs da caixa postal
 */
function selecionarTodas(obj){
    $(".check_recebida").each(function(){
        this.checked = obj.checked;
    });
}

function apagar(){
    var ids = ""; //Concatena IDs
    
    $(".check_recebida").each(function(){
        if(this.checked){
            if(ids != ""){
                ids += ",";
            }
            ids += this.value;
        }
    });
    
    //Verifica se algum ID foi selecionado
    if(ids == ""){
        alert("Selecione uma ou mais mensagens!");
        return false;
    }
    
    $.post(
        'caixapostal/apagarMensagens',
        {
            ids: ids
        },
        function(ret){
            if(ret.status){
                $("#grid_caixa_postal").trigger("reloadGrid");
                $("#selecionarRecebidas").removeAttr("checked");
            }
            alert(ret.msg);
        },
        'json'
    ).error(function(){
        alert("Falha na requisição ao SERVIDOR! Entre em contato com o Suporte.");
    });
}