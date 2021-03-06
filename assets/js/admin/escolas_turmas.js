$(document).ready(function(){
    //Habilitando componente de Abas
    $( "#abas" ).tabs({ active:0 });
    
    //Inicializando o Grid de Escolas (Aba Escolas & Turmas)
    $("#grid_escola").jqGrid({
        url: 'escolas/gridescolas',
        datatype: "json",
        colNames:['COD', 'Escola', 'Status', ''],
        colModel:[
                //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
                {name:'ID_ESCOLA', index:'ID_ESCOLA', width:25, align:'center', search: true, cellattr: site.formataGrid },
                {name:'NOME', index:'NOME', search: true},
                {name:'STATUS', index:'STATUS', width:50, align:'center', search: true, stype: 'select', searchoptions:{ value: "-1:Todas;1:Ativa;0:Inativa" }},
                {name:'Turmas', index:'Turmas', width:30, align:'center', search: false, sortable: false}
        ],
        rowNum:15,
        rowList:[15,25,35],
        pager: '#pg_escola',
        sortname: 'ID_ESCOLA',
        viewrecords: true,
        sortorder: "desc",
        caption:"Escolas",
        width: 750,
        height: 'auto',
        scrollOffset: 0
    });
                
    $("#grid_escola").filterToolbar();
                
    $("#grid_escola")
        .navGrid('#pg_escola',{edit:false,add:false,del:false,search:false})
        .navButtonAdd('#pg_escola',{
            caption: "Nova Escola", 
            buttonicon: "ui-icon-plus", 
            onClickButton: function(){ 
                //Abre modal
                $("#modal_escolas").dialog({
                    title: "Cadastrar Escola",
                    modal: true,
                    width: "550",
                    height: "280"
                });
            }, 
            position:"last"
    });
    //Fim do Grid Escolas
    
    
    //Formulário de escolas
    form = new Form();
    form.init('form_escola');
    form.initModal('escolas_turmas');
    
    //Formulário de Turmas
    formTurma = new Form();
    formTurma.init('form_turma');
    formTurma.initModal('escolas_turmas');
    
    
    
    /** Carrega dados da Aba Distibuir Listas */
    
    
    
    //Carrega Grid de Turmas (Aba Distribuir listas)
    $("#grid_turmas").jqGrid({
        url: 'escolas/gridturmas',
        datatype: "json",
        colNames:['', 'COD', 'Classe', 'Ensino', 'Ano', 'Período', 'Escola'],
        colModel:[
                //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
                {name:'ID_TURMA_2', index:'ID_TURMA_2', hidden: true },
                {name:'ID_TURMA', index:'ID_TURMA', width:15, align:'center', search: true, cellattr: site.formataGrid },
                {name:'CLASSE', index:'CLASSE', width:40, search: true},
                {name:'ENSINO', index:'ENSINO', width:30, search: true, stype: 'select', searchoptions:{ value: "T:Todos;F:Fundamental;M:Médio" }},
                {name:'ANO', index:'ANO', width:25, search: true, align:'center', stype: 'select', searchoptions:{ value: "T:Todos;1:1;2:2;3:3;4:4" }},
                {name:'PERIODO', index:'PERIODO', width:25, search: true, stype: 'select', searchoptions:{ value: "TO:Todos;M:Manhã;T:Tarde;N:Noite" }},
                {name:'ESCOLA', index:'ESCOLA', search: true}
        ],
        rowNum:10,
        rowList:[10,20,30],
        pager: '#pg_turmas',
        sortname: 'ESCOLA',
        viewrecords: true,
        sortorder: "ASC",
        caption:"Turmas",
        width: 750,
        height: 'auto',
        scrollOffset: 0,
        onSelectRow: function(id){ 
            //Armazena a turma selecionada
            $("#idTurmaSel").val(id); 
            //Ao selecionar uma linha o Grid de Listas é carregado com o ID_TURMA escolhido
            $("#grid_listas").setGridParam({url: 'escolas/gridlistas?ID_TURMA=' + id}); 
            $("#grid_listas").trigger("reloadGrid");
            $("#listas_inicio").hide();
            $("#listas").show();
            //Limpa filtros
            $("#selTodasListas").removeAttr("checked");
            $("#selListasUtilizadas").removeAttr("checked");
            //Limpa txt de status
            $("#statusListasTurmas").hide();
            $("#txtStatus").html("");
        }
    });
                
    $("#grid_turmas").filterToolbar();
    
    //Carrega Grid de Listas (Aba Distribuir listas)
    $("#grid_listas").jqGrid({
        url: 'escolas/gridlistas?ID_TURMA=0',
        datatype: "json",
        colNames:['', 'COD', 'Lista', 'Data Criação', 'Qtd Questões'],
        colModel:[
                //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
                {name:'ID_HISTORICO_GERADOC', index:'ID_HISTORICO_GERADOC', width:15, align:'center', search: false, cellattr: site.formataGrid, sortable: false },
                {name:'COD_LISTA', index:'COD_LISTA', width:15, align:'center', search: true },
                {name:'DESCR_ARQ', index:'DESCR_ARQ', width:40, search: true},
                {name:'DATA_REGISTRO', index:'DATA_REGISTRO', width:40, align:'center', search: false},
                {name:'NUM_QUESTOES', index:'NUM_QUESTOES', width:40, align:'center', search: false}
        ],
        rowNum:10,
        rowList:[10,20,30],
        pager: '#pg_listas',
        sortname: 'DATA_REGISTRO',
        viewrecords: true,
        sortorder: "DESC",
        caption:"Listas de Exercícios",
        width: 750,
        height: 'auto',
        scrollOffset: 0,
        jsonReader: {
            records: function(obj) { $("#idsListas").val(obj.idsListas); }
        }
    });
                
    $("#grid_listas").filterToolbar();
    
    
    
    //Carrega Grid de Listas (Aba Distribuir listas) - Segunda Visualização
    $("#grid_listas_turmas").jqGrid({
        url: 'escolas/gridlistas?ID_TURMA=0',
        datatype: "json",
        colNames:['', 'COD', 'Lista', 'Data Criação', 'Qtd Questões'],
        colModel:[
                //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
                {name:'ID_HISTORICO_GERADOC', index:'ID_HISTORICO_GERADOC', hidden:true },
                {name:'COD_LISTA', index:'COD_LISTA', width:15, align:'center', search: true, cellattr: site.formataGrid },
                {name:'DESCR_ARQ', index:'DESCR_ARQ', width:40, search: true},
                {name:'DATA_REGISTRO', index:'DATA_REGISTRO', width:40, align:'center', search: false},
                {name:'NUM_QUESTOES', index:'NUM_QUESTOES', width:40, align:'center', search: false},
                
        ],
        rowNum:10,
        rowList:[10,20,30],
        pager: '#pg_listas_turmas',
        sortname: 'DATA_REGISTRO',
        viewrecords: true,
        sortorder: "DESC",
        caption:"Listas de Exercícios",
        width: 750,
        height: 'auto',
        scrollOffset: 0,
        onSelectRow: function(id){ 
            //Armazena a turma selecionada
            $("#idListaSel").val(id); 
            //Ao selecionar uma linha o Grid de Listas é carregado com o ID_TURMA escolhido
            $("#grid_turmas_listas").setGridParam({url: 'escolas/gridturmas?ID_LISTA=' + id}); 
            $("#grid_turmas_listas").trigger("reloadGrid");
            $("#turmas_inicio").hide();
            $("#turmas").show();
            //Limpa filtros
            $("#selTodasTurmas").removeAttr("checked");
            $("#selTurmasUtilizadas").removeAttr("checked");
            //Limpa txt de status
            $("#statusListasTurmas").hide();
            $("#txtStatus").html("");
        }
    });
                
    $("#grid_listas_turmas").filterToolbar();
    
    //Carrega Grid de Turmas da segunda visualização
    $("#grid_turmas_listas").jqGrid({
        url: 'escolas/gridturmas?ID_LISTA=0',
        datatype: "json",
        colNames:['', '', 'Classe', 'Ensino', 'Ano', 'Período', 'Escola'],
        colModel:[
                //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
                {name:'ID_TURMA', index:'ID_TURMA', width:15, align:'center', search: false, cellattr: site.formataGrid, sortable: false },
                {name:'ID_TURMA_2', index:'ID_TURMA_2', hidden:true },
                {name:'CLASSE', index:'CLASSE', width:40, search: true},
                {name:'ENSINO', index:'ENSINO', width:30, search: true, stype: 'select', searchoptions:{ value: "T:Todos;F:Fundamental;M:Médio" }},
                {name:'ANO', index:'ANO', width:25, search: true, align:'center', stype: 'select', searchoptions:{ value: "T:Todos;1:1;2:2;3:3;4:4" }},
                {name:'PERIODO', index:'PERIODO', width:25, search: true, stype: 'select', searchoptions:{ value: "TO:Todos;M:Manhã;T:Tarde;N:Noite" }},
                {name:'ESCOLA', index:'ESCOLA', search: true}
        ],
        rowNum:10,
        rowList:[10,20,30],
        pager: '#pg_turmas',
        sortname: 'ESCOLA',
        viewrecords: true,
        sortorder: "ASC",
        caption:"Turmas",
        width: 750,
        height: 'auto',
        scrollOffset: 0,
        jsonReader: {
            records: function(obj) { $("#idsTurmas").val(obj.idsTurmas); }
        }
    });
                
    $("#grid_turmas_listas").filterToolbar();
});

/**
 * Função de callback do Formulário de Escolas
 */
function atualizarGridEscolas(ret, modalId){
    $("#grid_escola").trigger("reloadGrid");
    
    //Remove todas possíveis classes
    $("#form_escola_erros").removeClass("warning");
    $("#form_escola_erros").removeClass("success");
    $("#form_escola_erros").removeClass("error");
    
    if(ret.status){
        $("#form_escola_erros").addClass("success"); //Adiciona classe de sucesso
        $("#form_escola_erros_msg").html(ret.msg); //Adiciona mensagem
        $("#form_escola_erros").show(); //Exibe notificações
    }else{
        $("#form_escola_erros").addClass("error");
        $("#form_escola_erros_msg").html(ret.msg);
        $("#form_escola_erros").show();
    }
}

/**
 * Verifica retorno da operação de cadastro de turmas
 */
function verSalvarTurma(ret, modalId){
    //Remove todas possíveis classes
    $("#form_turma_erros").removeClass("warning");
    $("#form_turma_erros").removeClass("success");
    $("#form_turma_erros").removeClass("error");
        
    if(ret.status){
        $("#form_turma_erros").addClass("success"); //Adiciona classe de sucesso
        $("#form_turma_erros_msg").html(ret.msg); //Adiciona mensagem
        $("#form_turma_erros").show(); //Exibe notificações
        
        //Atualiza Turmas
        carregaListaTurmas($("#turmaIdEscola").val());
        
        //Zera Form
        novaTurma(false);
    }else{
        $("#form_turma_erros").addClass("error");
        $("#form_turma_erros_msg").html(ret.msg);
        $("#form_turma_erros").show();
    }
}

/**
 * Alterar o Status de ATIVO e INATIVO da Escola
 */
function alteraStatusEscola(ID_ESCOLA, status){
    //Abre aguarde
    site.aguarde();

    $.post(
        'escolas/alterarStatusEscola',
        {
            escolaIdCliente:    26436,
            escolaId:           ID_ESCOLA,
            escolaStatus:       status
        },
        function(ret){
            //Fecha aguarde
            site.fechaAguarde();
            
            if(!ret.status){
                $("#msg_escolas_turmas").html(ret.msg);
                $("#modal_escolas_turmas").trigger('click');
            }
        },
        'json'
    )
    .error(function(){
        $.fancybox.close();
        alert("Erro Fatal! Entre em conanto com o Administrador do Sistema!");
    });
}

/**
 * Abre o formulário de Edição de uma Turma existente
 */
function editarTurmas(ID_TURMA){
    //Carrega dados da Turma a ser Editada
    $("#turmaTitulo").html("Editar Turma");
    $("#turmaClasse").val($("#turmaClasse_" + ID_TURMA).val());
    
    $("input[name=turmaEnsino]").each(function(){
        if(this.value == $("#turmaEnsino_" + ID_TURMA).val()){
            this.checked = true;
        }else{
            this.checked = false;
        }
    });
    
    $("input[name=turmaPeriodo]").each(function(){
        if(this.value == $("#turmaPeriodo_" + ID_TURMA).val()){
            this.checked = true;
        }else{
            this.checked = false;
        }
    });
    
    $("#turmaAno").val($("#turmaAno_" + ID_TURMA).val());
    
    $("#turmaId").val(ID_TURMA);
    $("#btNovaTurma").css("display", "");
    $("#form_turma_erros").css("display", "none");
}

/**
 * Limpa o formulário de Turmas para que seja cadastrada uma Nova Turma
 */
function novaTurma(ocultarErros){
    //Carrega dados da Turma a ser Editada
    $("#turmaTitulo").html("Cadastrar Nova Turma");
    $("#turmaClasse").val("");
    
    $("input[name=turmaEnsino]").each(function(){
        if(this.value == 'M'){
            this.checked = true;
        }else{
            this.checked = false;
        }
    });
    
    $("input[name=turmaPeriodo]").each(function(){
        if(this.value == 'M'){
            this.checked = true;
        }else{
            this.checked = false;
        }
    });
    
    $("#turmaAno").val(1);
    $("#turmaId").val("");
    
    $("#btNovaTurma").css("display", "none");
    
    if(ocultarErros == true || ocultarErros == null || ocultarErros == undefined){
        $("#form_turma_erros").hide();
    }
}

/**
 * Abre o Modal de Turmas da Escola escolhida
 */
function turmas(ID_ESCOLA, ID_CLIENTE, ESCOLA){
    //Limpa formulário
    $("#turmaTitulo").html("Cadastrar Nova Turma");
    $("#turmaClasse").val("");
    $("#turmaEnsino").val("M");
    $("#turmaAno").val(1);
    $("#turmaPeriodo").val("M");
    $("#turmaId").val("");
    
    //Oculta erros
    $("#form_turma_erros").css("display", "none");
    
    //Abre modal
    $("#modal_turmas").dialog({
        title: "Turmas",
        modal: true,
        width: "800"
    });
    
    //Carrega lista de turmas do cliente, para escola escolhida
    carregaListaTurmas(ID_ESCOLA);
    
    //Carrega lista de Turmas
    $("#turmaEscola").html(ESCOLA);
    $("#form_turma #turmaIdEscola").val(ID_ESCOLA);
}

/**
 * Carrega todas as turmas e monta o HTML do UL das mesmas
 */
function carregaListaTurmas(ID_ESCOLA){
    $("#lista_turmas").html("Aguarde...");
    
    //Função que lista as Turmas da Escola selecionada
    $.post(
        'escolas/listarTurmas',
        {
            ID_ESCOLA: ID_ESCOLA
        },
        function (ret){
            if(!ret.status){
                $("#lista_turmas").html(ret.msg);
            }else{
                //Monta HTML do menu lateral de Turmas
                var html;
                html = "<table cellspacing='0' cellpadding='0'>";
                $(ret.turmas).each(function(){
                    html += "<thead><tr>";
                    html += "<th width='26%'><span class='icon gray small' data-icon='{'></span></th>";
                    html += "<th width='74%'>";
                    html += "<input type='hidden' id='turmaClasse_" + this.ID_TURMA + "' value='" + this.CLASSE + "' />";
                    html += "<input type='hidden' id='turmaEnsino_" + this.ID_TURMA + "' value='" + this.ENSINO + "' />";
                    html += "<input type='hidden' id='turmaAno_" + this.ID_TURMA + "' value='" + this.ANO + "' />";
                    html += "<input type='hidden' id='turmaPeriodo_" + this.ID_TURMA + "' value='" + this.PERIODO + "' />";
                    html += "<a href='javascript:void(0);' onclick='javascript:editarTurmas(" + this.ID_TURMA + ")'>" + this.CLASSE + "</a></th>";
                    html += "</tr></thead>";
                });
                html += "</table>";
                
                $("#lista_turmas").html(html);
            }
        },
        'json'
    );
}

/**
 * Altera a quantiodade de Anos de acordo com o Ensino selecionado
 */
function mudaAno(ensino){
    var html    = "";
    var limite  = 3; //Padrão, 3 anos
    
    //Se for Ensino Fundamental, limite vai para 4
    if(ensino == 'F'){
        limite = 4;
    }
    
    //Monta HTML de opções
    for(var i=1; i <= limite; i++){
        html += "<option value='"+i+"'>"+i+"º</option>";
    }
    
    //Popula <select> de Ano
    $("#turmaAno").html(html);
}

//Seleciona ou desmarca todas as opções do grid
function selListas(obj){
    //Tipo de operação a ser executada
    var tipo;
    
    //Verifica se o check foi marcado ou desmarcado
    if(obj.checked){
        $(".check_lista").attr("checked", "checked");
        tipo = "I"; //Inserir listas
    }else{
        $(".check_lista").removeAttr("checked");
        tipo = "E"; //Excluir listas
    }
    
    //Mensagem de aguarde
    $("#statusListasTurmas").hide();
    $("#statusListasTurmas").removeClass("success error warning");
    $("#statusListasTurmas").addClass("warning");
    $("#txtStatus").html("Aguarde...");
    $("#statusListasTurmas").show();
    
    $.post(
        'escolas/salvarTurmaLista',
        {
            idsTurmas: $("#idTurmaSel").val(),
            idsListas: $("#idsListas").val(), 
            tipo: tipo
        },
        function (ret){
            $("#statusListasTurmas").removeClass("success", "error", "warning");
            if(ret.status){
                $("#statusListasTurmas").addClass("success");
            }else{
                $("#statusListasTurmas").addClass("error");
            }
            $("#txtStatus").html(ret.msg);
        },
        'json'
    );
}

//Seleciona ou desmarca todas as opções do grid
function selTurmas(obj){
    //Tipo de operação a ser executada
    var tipo;
    
    //Verifica se o check foi marcado ou desmarcado
    if(obj.checked){
        $(".check_turma").attr("checked", "checked");
        tipo = "I"; //Inserir listas
    }else{
        $(".check_turma").removeAttr("checked");
        tipo = "E"; //Excluir listas
    }
    
    //Mensagem de aguarde
    $("#statusListasTurmas").hide();
    $("#statusListasTurmas").removeClass("success error warning");
    $("#statusListasTurmas").addClass("warning");
    $("#txtStatus").html("Aguarde...");
    $("#statusListasTurmas").show();
    
    $.post(
        'escolas/salvarTurmaLista',
        {
            idsTurmas: $("#idsTurmas").val(),
            idsListas: $("#idListaSel").val(),
            tipo: tipo
        },
        function (ret){
            $("#statusListasTurmas").removeClass("success error warning");
            if(ret.status){
                $("#statusListasTurmas").addClass("success");
            }else{
                $("#statusListasTurmas").addClass("error");
            }
            $("#txtStatus").html(ret.msg);
        },
        'json'
    );
}

/**
 * Função que salva a relação entre uma e uma turma (1:1)
 */
function salvaRelacaoLista(obj){
    //Tipo de operação a ser executada
    var tipo;
    
    //Mensagem de aguarde
    $("#statusListasTurmas").hide();
    $("#statusListasTurmas").removeClass("success error warning");
    $("#statusListasTurmas").addClass("warning");
    $("#txtStatus").html("Aguarde...");
    $("#statusListasTurmas").show();
    
    //Verifica se o check foi marcado ou desmarcado
    if(obj.checked){
        tipo = "I"; //Inserir lista
    }else{
        tipo = "E"; //Excluir lista
    }
    
    $.post(
        'escolas/salvarTurmaLista',
        {
            idsTurmas: $("#idTurmaSel").val(),
            idsListas: obj.value, 
            tipo: tipo
        },
        function (ret){
            $("#statusListasTurmas").removeClass("success error warning");
            if(ret.status){
                $("#statusListasTurmas").addClass("success");
            }else{
                $("#statusListasTurmas").addClass("error");
            }
            $("#txtStatus").html(ret.msg);
        },
        'json'
    );
}

/**
 * Função que salva a relação entre uma lista e uma turma (1:1)
 */
function salvaRelacaoTurma(obj){
    //Tipo de operação a ser executada
    var tipo;
    
    //Mensagem de aguarde
    $("#statusListasTurmas").hide();
    $("#statusListasTurmas").removeClass("success error warning");
    $("#statusListasTurmas").addClass("warning");
    $("#txtStatus").html("Aguarde...");
    $("#statusListasTurmas").show();
    
    //Verifica se o check foi marcado ou desmarcado
    if(obj.checked){
        tipo = "I"; //Inserir lista
    }else{
        tipo = "E"; //Excluir lista
    }
    
    $.post(
        'escolas/salvarTurmaLista',
        {
            idsTurmas: obj.value,
            idsListas: $("#idListaSel").val(), 
            tipo: tipo
        },
        function (ret){
            $("#statusListasTurmas").removeClass("success error warning");
            if(ret.status){
                $("#statusListasTurmas").addClass("success");
            }else{
                $("#statusListasTurmas").addClass("error");
            }
            $("#txtStatus").html(ret.msg);
        },
        'json'
    );
}

/**
 * Função que filtra para o grid de listas apenas as já utilizadas
 */
function exibeListasUtilizadas(obj){
    //Pega o ID da Turma selecionada
    var id = $("#idTurmaSel").val(); 
    var utilizadas;
    
    if(obj.checked){
        utilizadas = 1;
    }else{
        utilizadas = 0;
    }
    
    //Refaz o grid soliciando apenas selecionadas
    $("#grid_listas").setGridParam({url: 'escolas/gridlistas?ID_TURMA=' + id + '&utilizadas=' + utilizadas}); 
    $("#grid_listas").trigger("reloadGrid");
}

/**
 * Função que filtra para o grid de turmas apenas as já utilizadas
 */
function exibeTurmasUtilizadas(obj){
    //Pega o ID da Turma selecionada
    var id = $("#idListaSel").val(); 
    var utilizadas;
    
    if(obj.checked){
        utilizadas = 1;
    }else{
        utilizadas = 0;
    }
    
    //Refaz o grid soliciando apenas selecionadas
    $("#grid_turmas_listas").setGridParam({url: 'escolas/gridturmas?ID_LISTA=' + id + '&utilizadas=' + utilizadas}); 
    $("#grid_turmas_listas").trigger("reloadGrid");
}

/**
 * Função que controla o tipo de distribuição escolhido pelo usuário
 */
function exibeOpt(opt){
    $(".opts").hide();
    $("#" + opt).show();
}

/**
 * Efetua a verificação dos dados necessário para o envi ode convites e chama o script de disparo
 */
function enviaConvite(){
    var optSel      = null; //Armazena opção escolhida pelo user
    var id          = 0; //ID que será enviado para convites
    var idLista     = ""; //ID da lista para convites
    var erro        = ""; //Mensagem de erro
    verSms          = "N"; //Verifica o envio de sms
    
    //Verifica tela onde está sendo feito o envio.
    if($("input[name=tipoDistribuicao]:checked").val() == 'optTurmaLista'){
        var idTurmaSel  = $("#idTurmaSel").val();
        
        if(idTurmaSel == null || idTurmaSel <= 0){
            erro = "Selecione uma Turma para continuar!";
        }else{
            //Inicia varáveis para serem enviadas
            id          = idTurmaSel;
            optSel      = "T";
            idLista     = 0;
        }
    }else{
        var idListaSel = $("#idListaSel").val();

        if(idListaSel == null || idListaSel <= 0){
            erro = "Selecione uma Lista de Exercícios para continuar!";
        }else{
            //Inicia varáveis para serem enviadas
            id          = idListaSel;
            optSel      = "L";
            idLista     = idListaSel;
        }
    }

    //Se houver erros o script exibe a msg e para.
    if(id == 0){
        //Exibe modal com erro
        site.modal(erro, "Enviar Convite", "erro");
    }else{
        //Prossegue com o envio de convites
        $.post(
            'escolas/carregarInfoConvite',
            {
                id: id,
                tipo: optSel
            }, 
            function(ret){
                //Se houver erro no carregamento de informações
                if(!ret.status){
                    //Exibe modal com erro
                    site.modal(ret.msg, "Enviar Convite", "erro");
                }else{
                    var msgCel = "";
                    
                    //Verifica se existem usuários com celular
                    if(ret.qtdCel > 0){
                        msgCel  = "<strong style='color:red'>Atenção:</strong> Existe" + (ret.qtdCel > 1 ? "m" : "") + " <strong>" + ret.qtdCel + " aluno" + (ret.qtdCel > 1 ? "s" : "") + "</strong> com celular cadastrado para envio de convite por SMS.<br /><br />";
                        msgCel += "Isso irá consumir <strong>" + (ret.qtdCel*5) + " créditos</strong> da sua conta.<br /><br />";
                        msgCel += "Caso queira enviar os convites por SMS, marque a opção abaixo.<br /><br />";
                        msgCel += "<input type='checkbox' id='enviaCel' name='enviaCel' onclick='javascript:if(this.checked){ verSms = \"S\"; }else{ verSms = \"N\"; }'/> <strong>Sim, desejo enviar convites via SMS.</strong><br /><br />";
                    }
                    
                    site.modal(
                        "O convite será enviado por <strong>e-mail</strong> para <strong>" + ret.qtd + " aluno</strong>" + (ret.qtd > 1 ? "s" : "") + "." +
                        "<br /><br />" + msgCel +
                        "Deseja enviar convite" + (ret.qtd > 1 ? "s" : "") + " agora?"
                        , 
                        "Enviar Convite",
                        "",
                        {
                            'Sim': function() {
                                //Exibe mensagem de aguarde ao usuário
                                site.modal("<center style='font-weight:bold;'>Aguarde, enviando convites...</center>", "Enviar Convite");
                                
                                //Efetua a chamada do script de disparo
                                $.post(
                                    'escolas/dispararConvites', 
                                    {
                                        idsTurmas: ret.idsTurmas,
                                        idLista: idLista,
                                        sms: verSms
                                    }, 
                                    function(retDisparo){
                                        //Valida retorno
                                        if(retDisparo.status){
                                            site.modal("<center style='font-weight:bold;color:blue;'>" + retDisparo.msg + "</center>", "Enviar Convite");
                                        }else{
                                            site.modal(retDisparo.msg, "Enviar Convite", "erro");
                                        }
                                    }, 
                                    'json'
                                );
                            },
                            'Não': function() {
                                site.fechaModal();
                            }
                        }
                    );
                }
            },
            'json'
        ); //Fim .post
    } //Verificação de erros
}