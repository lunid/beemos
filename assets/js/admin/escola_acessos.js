$(document).ready(function(){
    //Carrega Grid de Acessos
    $("#grid_acessos").jqGrid({
        url: 'gridUsuariosAcesso',
        datatype: "json",
        hidegrid: false,
        colNames:['', 'Nome', 'Perfil', 'E-mail', 'Login',  'Data'],
        colModel:[
            //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
            {name:'ID_CLIENTE', index:'ID_CLIENTE', width:10, align:'center', search: false, cellattr: site.formataGrid, sortable: false },
            {name:'NOME_PRINCIPAL', index:'NOME_PRINCIPAL', width:80, search: true},
            {name:'FUNCAO', index:'FUNCAO', width:40, search: true},
            {name:'EMAIL', index:'EMAIL', width:80, search: true},
            {name:'LOGIN', index:'LOGIN', width:40, search: true},
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
            }
        ],
        rowNum:10,
        rowList:[10,20,30],
        pager: '#pg_acessos',
        sortname: 'NOME_PRINCIPAL',
        viewrecords: true,
        sortorder: "ASC",
        caption:"Contas de Acesso",
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
                        $("#form_acesso_erros").hide();
                        
                        //Ocultando Erros de Crédito
                        $("#creditos_erros").removeClass("success error warning");
                        $("#creditos_erros").hide();
                        $("input[name=opt_credito]").each(function(){
                            if(this.value == 1){
                                this.checked = true;
                            }else{
                                this.checked = false;
                            }
                        });
                        $("#optCreditoTxt").html("<strong style='color:blue'>Crédito:</strong>Incluir crédito(s)na conta do usuário atual.");
                        
                        
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
                        
                        //Informações
                        $("#totalAcessos").html(ret.usuario.totalAcessos);
                        $("#ultimoAcesso").html(ret.usuario.ultimoAcesso);
                        $("#docsGerados").html(ret.usuario.docsGerados);
                        $("#listasGeradas").html(ret.usuario.listasGeradas);
                        $("#saldoUsuario").html(ret.usuario.saldo);
                        $("#debitos").html(ret.usuario.debitos);
                        
                        //Créditos
                        $("#creditos").val(ret.usuario.limite);
                        $("#limite").html(ret.usuario.limite);
                        
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
                
    $("#grid_acessos").filterToolbar();
    
    $("#grid_acessos")
        .navGrid('#pg_acessos',{edit:false,add:false,del:false,search:false})
        .navButtonAdd('#pg_acessos',{
            caption: "Novo Usuário", 
            buttonicon: "ui-icon-plus", 
            onClickButton: function(){ 
                //Oculta erros e Excluir
                $("#form_acesso_erros").hide();
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
    formAcesso = new Form();
    formAcesso.init('form_acesso');
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
    $("#form_acesso_erros").removeClass("warning");
    $("#form_acesso_erros").removeClass("success");
    $("#form_acesso_erros").removeClass("error");
        
    if(ret.status){
        $("#form_acesso_erros").addClass("success"); //Adiciona classe de sucesso
        $("#form_acesso_erros_msg").html(ret.msg); //Adiciona mensagem
        $("#form_acesso_erros").show(); //Exibe notificações
        
        $("#grid_usuarios").trigger('reloadGrid');
    }else{
        $("#form_acesso_erros").addClass("error");
        $("#form_acesso_erros_msg").html(ret.msg);
        $("#form_acesso_erros").show();
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

/**
 * Excluir um Cargo/Função
 */
function excluirCargo(idCargo){
    if(confirm("Tem certeza que deseja excluir o Cargo/Função?")){
        site.aguarde();
    
        $.post(
            'excluirCargo',
            {
                idCargo: idCargo
            },
            function(ret){
                site.fechaAguarde();

                if(ret.status){
                    $("#grid_cargos").trigger('reloadGrid');
                    formUsuario.clearForm();
                    $("#modal_cargos").dialog("close");
                }else{
                    $("#form_cargo_erros").removeClass("warning success error");
                    $("#form_cargo_erros").addClass("error");
                    $("#form_cargo_erros_msg").html(ret.msg);
                    $("#form_cargo_erros").show();
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

function verOptCredito(opt){
    var txt = "";
    
    //Se crédito
    if(opt == 1){
        txt = "<strong style='color:blue'>Crédito:</strong>Incluir crédito(s)na conta do usuário atual.";
    }else if(opt == 2){
        //Se débito
        txt = "<strong style='color:red'>Débito:</strong>Retirar crédito(s) da conta do usuário atual.";
    }
    
    $("#optCreditoTxt").html(txt);
}

function executaOperacaoCredito(){
    var operacao    = $("input[name=opt_credito]:checked").val();
    var creditos    = $("#creditos").val();
    var idCliente   = $("#ID_CLIENTE").val();
    
    //Ocultando Erros
    $("#creditos_erros").removeClass("success error warning");
    $("#creditos_erros").hide();
    
    //Validação
    if(creditos <= 0){
        $("#creditos_erros").addClass("warning");
        $("#creditos_erros_msg").html("Para efetuar a operação, digite um número de créditos maior que zero!");
        $("#creditos_erros").show();
        return false;
    }
    
    site.aguarde();
    
    $.post(
        "executaOperacaoCredito",
        {
            idCliente: idCliente,
            operacao:operacao,
            creditos:creditos
        },
        function(ret){
            site.fechaAguarde();
            
            //Verifica o retorno
            if(ret.status){
                //Adiciona classe de sucesso
                $("#creditos_erros").addClass("success");
                $("#creditos").val("");
            }else{
                //Adiciona classe de erros
                $("#creditos_erros").addClass("error");
            }
            
            //Adiciona MSG ao HTML e exibe notificação
            $("#creditos_erros_msg").html(ret.msg);
            $("#creditos_erros").show();
        },
        'json'
    ).error(
        function(){
            site.fechaAguarde();
            alert("Falha no servidor! Entre em contato com o Suporte.");
        }
    );
}