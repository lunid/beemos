$(document).ready(function(){
    //Habilitando componente de Abas
    $( "#abas" ).tabs();
    $( "#modal_usuario" ).tabs();
    
    //Carrega Grid de Listas - Aba principal
    $("#grid_usuarios").jqGrid({
        url: 'gridusuarios',
        datatype: "json",
        hidegrid: false,
        colNames:['', 'Nome Completo', 'Cargo/Função', 'E-mail', 'Login', 'Bloq', 'Data', 'Carga'],
        colModel:[
            //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
            {name:'ID_CLIENTE', index:'ID_CLIENTE', width:10, align:'center', search: false, cellattr: site.formataGrid, sortable: false },
            {name:'NOME_PRINCIPAL', index:'NOME_PRINCIPAL', width:80, search: true},
            {name:'FUNCAO', index:'FUNCAO', width:40, search: true},
            {name:'EMAIL', index:'EMAIL', width:80, search: true},
            {name:'LOGIN', index:'LOGIN', width:40, search: true},
            {name:'BLOQ', index:'BLOQ', width:30, search: true, stype:'select', searchoptions:{ value: "0:Todos;1:Bloqueado;2:Não Bloqueados" }, align:'center'},
            {
                name:'DATA_REGISTRO', index:'DATA_REGISTRO', width:30, align:'center', search: true,
                searchoptions:{
                    dataInit:function(elem){
                        $(elem).datepicker({
                            onSelect: function() {
                                $("#grid_usuarios")[0].triggerToolbar();
                            }
                        });
                    }
                }
            },
            {name:'CARGA', index:'CARGA', width:15, align:'center', search: false, sortable: false}
        ],
        rowNum:10,
        rowList:[10,20,30],
        pager: '#pg_usuarios',
        sortname: 'NOME_PRINCIPAL',
        viewrecords: true,
        sortorder: "ASC",
        caption:"Usuários",
        width: 900,
        height: 'auto',
        scrollOffset: 0,
        ondblClickRow: function(rowid, iRow, iCol, e){
            //Verifica ID
            if(rowid <= 0){
                alert("Selecione um usuário para edição!");
                return false;
            }
            
            //Aguarde 
            site.aguarde();
            
            //Carrega dados via Ajax
            $.post(
                "carregarDadosUsuario",
                {
                    idCliente: rowid
                },
                function(ret){
                    //Esconde aguarde
                    site.fechaAguarde();
                    
                    //Valida retorno jSon
                    if(ret.status){
                        //Seleciona aba de Dados
                        $("#aba_dados").trigger('click');

                        //Esconde abas desnecessárias
                        $("#painel_abas").show();
                        
                        //Oculta Erros e exibe excluir
                        $("#form_usuario_erros").hide();
                        
                        //Ação do Botão Excluir
                        $("#btExcluirUsuario").bind('click', null, function(){
                            excluirUsuario(rowid, true);
                        });
                        $("#btExcluirUsuario").show();
                        
                        //Carrega dados do usuário
                        $("#ID_CLIENTE").val(ret.usuario.ID_CLIENTE);
                        $("#NOME_PRINCIPAL").val(ret.usuario.NOME_PRINCIPAL);
                        $("#EMAIL").val(ret.usuario.EMAIL);
                        $("#LOGIN").val(ret.usuario.LOGIN);
                        $("#APELIDO").val(ret.usuario.APELIDO);
                        $("#SEL_ID_AUTH_FUNCAO").val(ret.usuario.ID_AUTH_FUNCAO);
                        
                        //Controles de senha
                        $("#PASSWD").val("");
                        $("#C_PASSWD").val("");
                        $("#SENHA_SISTEMA").removeAttr("checked");
                        $("#SENHA_SISTEMA").val("0");
                        $("#SENHA_CADASTRO").hide();
                        $(".SENHA_NOVA").removeAttr("checked");
                        $(".SENHA_NOVA").val("0");
                        $("#SENHA_ALTERACAO").show();
                        $("#senha").hide();
                        
                        //Abre modal
                        $("#modal_usuario").dialog({
                            title: "Editar Usuário",
                            modal: true,
                            width: "550",
                            height: "550"
                        });
                    }else{
                        alert(ret.msg);
                    }
                },
                'json'
            ).error(
                function(){
                    site.fechaAguarde();
                    alert("Falha no servidor! Entre em contato com o Suporte.");
                }
            );
        }
    });
                
    $("#grid_usuarios").filterToolbar();
    
    $("#grid_usuarios")
        .navGrid('#pg_usuarios',{edit:false,add:false,del:false,search:false})
        .navButtonAdd('#pg_usuarios',{
            caption: "Novo Usuário", 
            buttonicon: "ui-icon-plus", 
            onClickButton: function(){ 
                //Seleciona aba de Dados
                $("#aba_dados").trigger('click');
                
                //Esconde abas desnecessárias
                $("#painel_abas").hide();
                
                //Oculta erros e Excluir
                $("#form_usuario_erros").hide();
                $("#btExcluirUsuario").hide();
                
                //Limpa form
                formUsuario.clearForm();
                
                //Controles de senha
                $("#PASSWD").val("");
                $("#C_PASSWD").val("");
                $("#SENHA_SISTEMA").attr("checked", "checked");
                $("#SENHA_SISTEMA").val("1");
                $("#SENHA_CADASTRO").show();
                $(".SENHA_NOVA").removeAttr("checked");
                $(".SENHA_NOVA").val("0");
                $("#SENHA_ALTERACAO").hide();
                $("#senha").hide();
                
                //Abre modal
                $("#modal_usuario").dialog({
                    title: "Novo Usuário",
                    modal: true,
                    width: "550",
                    height: "550"
                });
            }, 
            position:"last"
    });
    
    //Inicia formulário de Usuários
    formUsuario = new Form();
    formUsuario.init('form_usuario');
    
    //Inicia formulário de Cargos
    formCargo = new Form();
    formCargo.init('form_cargo');
    $("#LIM_CREDITO").mask("9?9999");
});

/**
 * Altera status de bloqueio do usuário
 */
function bloquearUsuario(idCliente, status){
    site.aguarde();
    
    $.post(
        'bloquearUsuario',
        {
            idCliente: idCliente,
            status: status                
        },
        function(ret){
            site.fechaAguarde();
            
            if(ret.status){
                $("#grid_usuarios").trigger('reloadGrid');
            }else{
                alert(ret.msg);
            }
        },
        'json'
    ).error(
        function(){
            site.fechaAguarde();
            alert("Falha no servidor! Entre em contato com o Suporte.");
        }
    );
}

/**
 * Excluir um ou mais usuário(s)
 */
function excluirUsuario(idCliente, modal){
    if(confirm("Tem certeza que deseja excluir o(s) Usuário(s)?")){
        site.aguarde();
    
        $.post(
            'excluirUsuario',
            {
                idCliente: idCliente
            },
            function(ret){
                site.fechaAguarde();

                if(ret.status){
                    $("#grid_usuarios").trigger('reloadGrid');

                    if(modal == true){
                        formUsuario.clearForm();
                        $("#modal_usuario").dialog("close");
                    }
                }else{
                    alert(ret.msg);
                }
            },
            'json'
        ).error(
            function(){
                site.fechaAguarde();
                alert("Falha no servidor! Entre em contato com o Suporte.");
            }
        );
    }
}

/**
 * Excluir um ou mais usuário(s)
 */
function enviarLinkAcesso(idCliente){
    //Abre modal explicando ação ao usuário
    site.modal("Ao enviar o <b>link de acesso</b> os usuários selecionados serão obrigados a cadastrar uma nova senha em seu próximo acesso ao sistema.<br/><br/>Tem certeza que deseja enviar o link de acesso?", "Link de Acesso", null, 
            {
                "Sim": function() {
                    site.aguarde();
                    tmpModal = this;
                    $.post(
                        "enviarLinkAcesso",
                        {idCliente:idCliente},
                        function(ret){
                            site.fechaAguarde();
                                                       
                            //Fecha modal
                            $(tmpModal).dialog( "close" );
                            //Exibe retorno
                            alert(ret.msg);
                        },
                        'json'
                    ).error(
                        function(){
                            site.fechaAguarde();
                            alert("Falha no servidor! Entre em contato com o Suporte.");
                        }
                    );
                },
                "Não": function() {
                    $( this ).dialog( "close" );
                }
            }, 400);
}

/**
 * Verifica a geração de senha manual ou automática
 */
function checkSenha(obj){
    //Define valor do objeto
    obj.value = obj.checked ? 1 : 0;
    
    //Limpa os campos de senha
    $("#PASSWD").val("");
    $("#C_PASSWD").val("");
    if(obj.checked){
        //Esconde campos de senha
        $("#ENVIAR_ACESSO").val("0");
        $("#ENVIAR_ACESSO").removeAttr("checked");
        $("#senha").css("display", "none");
    }else{
        //Exibe campos de senha
        $("#ENVIAR_ACESSO").val("1");
        $("#ENVIAR_ACESSO").attr("checked", "checked");
        $("#senha").css("display", "");
    }
}

/**
 * Verifica retorno da operação de cadastro de usuários
 */
function verSalvarUsuario(ret, modalId){
    //Remove todas possíveis classes
    $("#form_usuario_erros").removeClass("warning");
    $("#form_usuario_erros").removeClass("success");
    $("#form_usuario_erros").removeClass("error");
        
    if(ret.status){
        $("#form_usuario_erros").addClass("success"); //Adiciona classe de sucesso
        $("#form_usuario_erros_msg").html(ret.msg); //Adiciona mensagem
        $("#form_usuario_erros").show(); //Exibe notificações
        
        $("#grid_usuarios").trigger('reloadGrid');
    }else{
        $("#form_usuario_erros").addClass("error");
        $("#form_usuario_erros_msg").html(ret.msg);
        $("#form_usuario_erros").show();
    }
}

/**
 * Checa as opções de senha para formulário de alteração do usuário
 */
function checkSenhaAlt(obj){
    //Limpa os campos de senha
    $("#PASSWD").val("");
    $("#C_PASSWD").val("");
    
    //Armazena ID utilizado se a oção for marcada
    var id = obj.checked ? obj.id : null;
    
    $(".SENHA_NOVA").removeAttr("checked");
    $(".SENHA_NOVA").val("0");
    
    if(id != null){
        $("#" + id).attr("checked", "checked");
        $("#" + id).val("1");
        
        if(id == "SENHA_NOVA_MANUAL"){
            $("#ENVIAR_ACESSO").val("1");
            $("#ENVIAR_ACESSO").attr("checked", "checked");
            $("#senha").show();
        }else{
            $("#ENVIAR_ACESSO").val("0");
            $("#ENVIAR_ACESSO").removeAttr("checked");
            $("#senha").hide();
        }
    }else{
        $("#senha").hide();
    }
}

/**
 * Marca ou Desmarca todas as opções de grid
 */
function selTodos(obj){
    if(obj.checked){
        $(".checkGrid").attr("checked", "checked");
    }else{
        $(".checkGrid").removeAttr("checked");
    }
}

function excutaAcao(){
    var ids = "";
    $(".checkGrid:checked").each(function(){
        if(ids != ""){
            ids += ",";
        }
        
        ids += this.value;
    });
    
    //Verifica se foi selecionado algum usuário
    if(ids == ""){
        alert("Selecione no mínimo um Usuário!");
        return false;
    }
    
    //Valida ação
    if($("#acaoMassa").val() <= 0){
        alert("Selecione uma Ação!");
        return false;
    }

    //Executa ação solicitada
    switch($("#acaoMassa").val()){
        case '1':
            bloquearUsuario(ids, 1);
            break;
        case '2':
            bloquearUsuario(ids, 0);
            break;
        case '3':
            excluirUsuario(ids, false);
            break;
        case '4':
            enviarLinkAcesso(ids);
            break;
    }
}

function carregaCargos(){
    //Carrega Grid de Listas - Aba principal
    $("#grid_cargos").jqGrid({
        url: 'gridcargos',
        datatype: "json",
        hidegrid: false,
        colNames:['', 'Cargo/Função', 'Código', 'Limite/Crédito', 'Recaraga'],
        colModel:[
            //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
            {name:'ID_AUTH_FUNCAO', index:'ID_AUTH_FUNCAO', width:8, align:'right', search: false, cellattr: site.formataGrid, sortable: false },
            {name:'FUNCAO', index:'FUNCAO', width:50, search: true},
            {name:'CODIGO', index:'CODIGO', width:10, search: true, align:'right'},
            {name:'LIM_CREDITO', index:'LIM_CREDITO', width:8, search: true, align:'right'},
            {name:'RECARGA_AUTO', index:'RECARGA_AUTO', width:8, search: true, align:'center', stype:'select', searchoptions:{ value: "0:Todos;1:Sim;2:Não" }}
        ],
        rowNum:10,
        rowList:[10,20,30],
        pager: '#pg_cargos',
        sortname: 'FUNCAO',
        viewrecords: true,
        sortorder: "ASC",
        caption:"Cargos e Funções",
        width: 900,
        height: 'auto',
        scrollOffset: 0,
        ondblClickRow: function(rowid, iRow, iCol, e){
            //Verifica ID
            if(rowid <= 0){
                alert("Selecione um Cargo/Função para edição!");
                return false;
            }
            
            //Aguarde 
            site.aguarde();
            
            //Carrega dados via Ajax
            $.post(
                "carregarCargo",
                {
                    idCargo: rowid
                },
                function(ret){
                    //Esconde aguarde
                    site.fechaAguarde();
                    
                    //Valida retorno jSon
                    if(ret.status){
                        //Oculta Erros e exibe excluir
                        $("#form_cargo_erros").hide();
                        
                        //Ação do Botão Excluir
                        $("#btExcluirCargo").bind('click', null, function(){
                            excluirCargo(rowid, true);
                        });
                        $("#btExcluirCargo").show();
                        
                        //Carrega dados do usuário
                        $("#ID_AUTH_FUNCAO").val(ret.cargo.ID_AUTH_FUNCAO);
                        $("#FUNCAO").val(ret.cargo.FUNCAO);
                        $("#CODIGO").val(ret.cargo.CODIGO);
                        $("#LIM_CREDITO").val(ret.cargo.LIM_CREDITO);
                        
                        $("input[name=RECARGA_AUTO]").removeAttr("checked");
                        
                        if(ret.cargo.RECARGA_AUTO == 1){
                            $("#RECARGA_AUTO_SIM").attr("checked", "checked");
                        }else{
                            $("#RECARGA_AUTO_NAO").attr("checked", "checked");
                        }
                        
                        //Abre modal
                        $("#modal_cargos").dialog({
                            title: "Editar Cargo/Função",
                            modal: true,
                            width: "550",
                            height: "550"
                        });
                    }else{
                        alert(ret.msg);
                    }
                },
                'json'
            ).error(
                function(){
                    site.fechaAguarde();
                    alert("Falha no servidor! Entre em contato com o Suporte.");
                }
            );
        }
    });
                
    $("#grid_cargos").filterToolbar();
    
    $("#grid_cargos")
        .navGrid('#pg_cargos',{edit:false,add:false,del:false,search:false})
        .navButtonAdd('#pg_cargos',{
            caption: "Novo Cargo/Função", 
            buttonicon: "ui-icon-plus", 
            onClickButton: function(){ 
                //Oculta erros e Excluir
                $("#form_cargo_erros").hide();
                $("#btExcluirCargo").hide();
                
                //Limpa form
                formCargo.clearForm();
                $("input[name=RECARGA_AUTO]").removeAttr("checked");
                $("#RECARGA_AUTO_SIM").attr("checked", "checked");
                
                //Abre modal
                $("#modal_cargos").dialog({
                    title: "Novo Cargo/Função",
                    modal: true,
                    width: "550",
                    height: "550"
                });
            }, 
            position:"last"
    });
}

/**
 * Verifica retorno da operação de cadastro de cargos
 */
function verSalvarCargo(ret, modalId){
    //Remove todas possíveis classes
    $("#form_cargo_erros").removeClass("warning success error");
        
    if(ret.status){
        $("#form_cargo_erros").addClass("success"); //Adiciona classe de sucesso
        $("#form_cargo_erros_msg").html(ret.msg); //Adiciona mensagem
        $("#form_cargo_erros").show(); //Exibe notificações
        
        $("#grid_cargos").trigger('reloadGrid');
    }else{
        $("#form_cargo_erros").addClass("error");
        $("#form_cargo_erros_msg").html(ret.msg);
        $("#form_cargo_erros").show();
    }
}

/**
 * Altera status de bloqueio do usuário
 */
function alterarRecargaCargo(idCargo, status){
    site.aguarde();
    
    $.post(
        'alterarRecargaCargo',
        {
            idCargo: idCargo,
            status: status
        },
        function(ret){
            site.fechaAguarde();
            
            if(ret.status){
                $("#grid_cargos").trigger('reloadGrid');
            }else{
                alert(ret.msg);
            }
        },
        'json'
    ).error(
        function(){
            site.fechaAguarde();
            alert("Falha no servidor! Entre em contato com o Suporte.");
        }
    );
}
