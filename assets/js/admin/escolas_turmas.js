$(document).ready(function(){
    //Habilitando componente de Abas
    $( "#abas" ).tabs({ active:0 });
    
    //Inicializando o Grid
    $("#grid_escola").jqGrid({
        url: 'GridEscolas',
        datatype: "json",
        colNames:['COD', 'Escola', 'Status', ''],
        colModel:[
                {name:'ID_ESCOLA', index:'ID_ESCOLA', width:25, align:'center', search: true},
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