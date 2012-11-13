$(document).ready(function(){
    //Habilitando componente de Abas
    $( "#abas" ).tabs({ active:0 });
    
    //Inicializando o Grid de Escolas (Aba Escolas & Turmas)
    $("#grid_escola").jqGrid({
        url: 'GridEscolas',
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
                $.fancybox.open([
                    {
                        href: '#modal_escolas',
                        helpers: {
                            overlay : {
                                closeClick : false
                            }
                        },
                        height: 40
                    }
                ]);
            }, 
            position:"last"
    });
    
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
        url: 'GridTurmas',
        datatype: "json",
        colNames:['COD', 'Classe', 'Ensino', 'Ano', 'Período', 'Escola'],
        colModel:[
                //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
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
            $("#grid_listas").setGridParam({url: 'GridListas?ID_TURMA=' + id}); 
            $("#grid_listas").trigger("reloadGrid");
            $("#listas_inicio").hide();
            $("#listas").show();
            //Limpa filtros
            $("#selTodasListas").removeAttr("checked");
            $("#selListasUtilizadas").removeAttr("checked");
            //Limpa txt de status
            $("#txtStatus").html("");
        }
    });
                
    $("#grid_turmas").filterToolbar();
    
    //Carrega Grid de Listas (Aba Distribuir listas)
    $("#grid_listas").jqGrid({
        url: 'GridListas?ID_TURMA=0',
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
});

/**
 * Função de callback do Formulário de Escolas
 */
function atualizaGridEscolas(ret, modalId){
    $("#grid_escola").trigger("reloadGrid");
    
    if(ret.status){
        if(modalId != ""){
            $("#msg_" + modalId).html(ret.msg);
            $("#modal_" + modalId).trigger('click');
        }else{
            alert(ret.msg);
        }
    }else{
        $("#form_escola_erros_msg").html(ret.msg);
        $("#form_escola_erros").css("display", "");
    }
}

function verSalvarTurma(ret, modalId){
    if(ret.status){
        if(modalId != ""){
            $("#msg_" + modalId).html(ret.msg);
            $("#modal_" + modalId).trigger('click');
        }else{
            alert(ret.msg);
        }
    }else{
        $("#form_turma_erros_msg").html(ret.msg);
        $("#form_turma_erros").css("display", "");
    }
}

/**
 * Alterar o Status de ATIVO e INATIVO da Escola
 */
function alteraStatusEscola(ID_ESCOLA, status){
    //Abre aguarde
    site.aguarde();

    $.post(
        'alteraStatusEscola',
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
    $("#turmaEnsino").val($("#turmaEnsino_" + ID_TURMA).val());
    $("#turmaAno").val($("#turmaAno_" + ID_TURMA).val());
    $("#turmaPeriodo").val($("#turmaPeriodo_" + ID_TURMA).val());
    $("#turmaId").val(ID_TURMA);
    
    $("#btNovaTurma").css("display", "");
    $("#form_turma_erros").css("display", "none");
}

/**
 * Limpa o formulário de Turmas para que seja cadastrada uma Nova Turma
 */
function novaTurma(){
    //Carrega dados da Turma a ser Editada
    $("#turmaTitulo").html("Cadastrar Nova Turma");
    $("#turmaClasse").val("");
    $("#turmaEnsino").val("M");
    $("#turmaAno").val(1);
    $("#turmaPeriodo").val("M");
    $("#turmaId").val("");
    
    $("#btNovaTurma").css("display", "none");
    $("#form_turma_erros").css("display", "none");
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
    
    $("#form_turma_erros").css("display", "none");
    
    //Abre modal
    $.fancybox.open([
        {
            href: '#modal_turmas',
            helpers: {
                overlay : {
                    closeClick : false
                }
            }
        }
    ]);
    
    //Carrega lista de turmas do cliente, para escola escolhida
    carregaListaTurmas(ID_ESCOLA, ID_CLIENTE);
    
    //Carrega lista de Turmas
    $("#turmaEscola").html(ESCOLA);
    $("#form_turma #turmaIdEscola").val(ID_ESCOLA);
    $("#form_turma #turmaIdCliente").val(ID_CLIENTE);
}

/**
 * Carrega todas as turmas e monta o HTML do UL das mesmas
 */
function carregaListaTurmas(ID_ESCOLA, ID_CLIENTE){
    $("#lista_turmas").html("Aguarde...");
    
    //Função que lista as Turmas da Escola selecionada
    $.post(
        'listaTurmas',
        {
            ID_ESCOLA: ID_ESCOLA,
            ID_CLIENTE: ID_CLIENTE
        },
        function (ret){
            if(!ret.status){
                $("#lista_turmas").html(ret.msg);
            }else{
                //Monta HTML do menu lateral de Turmas
                var html;
                html = "<ul style='list-style:circle;'>";
                $(ret.turmas).each(function(){
                    html += "<li>";
                    html += "<input type='hidden' id='turmaClasse_" + this.ID_TURMA + "' value='" + this.CLASSE + "' />";
                    html += "<input type='hidden' id='turmaEnsino_" + this.ID_TURMA + "' value='" + this.ENSINO + "' />";
                    html += "<input type='hidden' id='turmaAno_" + this.ID_TURMA + "' value='" + this.ANO + "' />";
                    html += "<input type='hidden' id='turmaPeriodo_" + this.ID_TURMA + "' value='" + this.PERIODO + "' />";
                    html += "<a href='javascript:void(0);' onclick='javascript:editarTurmas(" + this.ID_TURMA + ")'>" + this.CLASSE + "</a>";
                    html += "</li>";
                });
                html += "</ul>";
                
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
        html += "<option value='"+i+"'>"+i+"</option>";
    }
    
    //Popula <select> de Ano
    $("#turmaAno").html(html);
}

//Seleciona o desmarca todas as opções do grid
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
    $("#txtStatus").html("Aguarde...");
    
    $.post(
        'salvaTurmaLista',
        {
            idTurma: $("#idTurmaSel").val(),
            idsListas: $("#idsListas").val(), 
            tipo: tipo
        },
        function (ret){
            if(ret.status){
                $("#txtStatus").html(ret.msg);
            }else{
                $("#txtStatus").html("<span style='color:red'>" + ret.msg + "</span>");
            }
        },
        'json'
    );
}

function salvaRelacaoLista(obj){
    //Tipo de operação a ser executada
    var tipo;
    
    //Mensagem de aguarde
    $("#txtStatus").html("Aguarde...");
    
    //Verifica se o check foi marcado ou desmarcado
    if(obj.checked){
        tipo = "I"; //Inserir lista
    }else{
        tipo = "E"; //Excluir lista
    }
    
    $.post(
        'salvaTurmaLista',
        {
            idTurma: $("#idTurmaSel").val(),
            idsListas: obj.value, 
            tipo: tipo
        },
        function (ret){
            if(ret.status){
                $("#txtStatus").html(ret.msg);
            }else{
                $("#txtStatus").html("<span style='color:red'>" + ret.msg + "</span>");
            }
        },
        'json'
    );
}

function exibeUtilizadas(obj){
    //Pega o ID da Turma selecionada
    var id = $("#idTurmaSel").val(); 
    var utilizadas;
    
    if(obj.checked){
        utilizadas = 1;
    }else{
        utilizadas = 0;
    }
    
    //Refaz o grid soliciando apenas selecionadas
    $("#grid_listas").setGridParam({url: 'GridListas?ID_TURMA=' + id + '&utilizadas=' + utilizadas}); 
    $("#grid_listas").trigger("reloadGrid");
}