$(document).ready(function(){
    //Carrega Grid de Acessos
    $("#grid_acessos").jqGrid({
        url: 'gridUsuariosAcesso',
        datatype: "json",
        hidegrid: false,
        colNames:['', 'Nome', 'Perfil', 'E-mail', 'Login',  'Data Registro', 'Bloqueio'],
        colModel:[
            //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
            {name:'ID_USUARIO', index:'ID_USUARIO', width:10, align:'center', search: false, cellattr: site.formataGrid, sortable: false },
            {name:'NOME', index:'NOME', width:80, search: true},
            {name:'PERFIL', index:'PERFIL', width:40, search: true},
            {name:'EMAIL', index:'EMAIL', width:80, search: true},
            {name:'LOGIN', index:'LOGIN', width:40, search: true},
            {
                name:'DATA_REGISTRO', index:'DATA_REGISTRO', width:40, align:'center', search: true,
                searchoptions:{
                    dataInit:function(elem){
                        $(elem).datepicker({
                            onSelect: function() {
                                $("#grid_acessos")[0].triggerToolbar();
                            }
                        });
                    }
                }
            },
            {name:'BLOQ', index:'BLOQ', width:30, search: true, stype:'select', searchoptions:{ value: "0:Todos;1:Bloqueado;2:Não Bloqueados" }, align:'center'}
        ],
        rowNum:10,
        rowList:[10,20,30],
        pager: '#pg_acessos',
        sortname: 'NOME',
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
                "carregarDadosUsuarioAcesso",
                {
                    idUsuario: rowid
                },
                function(ret){
                    //Esconde aguarde
                    site.fechaAguarde();
                    
                    //Valida retorno jSon
                    if(ret.status){
                        //Oculta Erros e exibe excluir
                        $("#form_acesso_erros").hide();
                        
                        //Ação do Botão Excluir
                        $("#btExcluirUsuario").bind('click', null, function(){
                            excluirUsuario(rowid, true);
                        });
                        $("#btExcluirUsuario").show();
                        
                        //Carrega dados do usuário
                        $("#ID_USUARIO").val(ret.usuario.ID_USUARIO);
                        $("#NOME").val(ret.usuario.NOME);
                        $("#EMAIL").val(ret.usuario.EMAIL);
                        $("#LOGIN").val(ret.usuario.LOGIN);
                        $("#TELEFONE").val(ret.usuario.TELEFONE);
                        $("#SEL_ID_PERFIL").val(ret.usuario.ID_PERFIL);
                        $("#SENHA").val("");
                        $("#C_SENHA").val("");
                        
                        //Controle de senha
                        $("#senhaMsg").show();
                        $("#SENHA").removeClass("required");
                        $("#C_SENHA").removeClass("required");
                        
                        //Abre modal
                        $("#modal_acesso").dialog({
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
                formAcesso.clearForm();
                
                //Controle de senha
                $("#senhaMsg").hide();
                $("#SENHA").addClass("required");
                $("#C_SENHA").addClass("required");
                
                //Abre modal
                $("#modal_acesso").dialog({
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
    
    //Máscara
    $("#TELEFONE").mask("(99)99999-999?9");    
});

/**
 * Altera status de bloqueio do usuário
 */
function bloquearUsuario(idUsuario, status){
    site.aguarde();
    
    $.post(
        'bloquearUsuarioAcesso',
        {
            idUsuario: idUsuario,
            status: status                
        },
        function(ret){
            site.fechaAguarde();
            
            if(ret.status){
                $("#grid_acessos").trigger('reloadGrid');
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
function excluirUsuario(idUsuario, modal){
    if(confirm("Tem certeza que deseja excluir o Usuário?")){
        site.aguarde();
    
        $.post(
            'excluirUsuarioAcesso',
            {
                idUsuario: idUsuario
            },
            function(ret){
                site.fechaAguarde();

                if(ret.status){
                    $("#grid_acessos").trigger('reloadGrid');

                    if(modal == true){
                        formAcesso.clearForm();
                        $("#modal_acesso").dialog("close");
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
 * Verifica retorno da operação de cadastro de usuários
 */
function verSalvarAcesso(ret, modalId){
    //Remove todas possíveis classes
    $("#form_acesso_erros").removeClass("warning");
    $("#form_acesso_erros").removeClass("success");
    $("#form_acesso_erros").removeClass("error");
        
    if(ret.status){
        $("#form_acesso_erros").addClass("success"); //Adiciona classe de sucesso
        $("#form_acesso_erros_msg").html(ret.msg); //Adiciona mensagem
        $("#form_acesso_erros").show(); //Exibe notificações
        
        $("#grid_acessos").trigger('reloadGrid');
    }else{
        $("#form_acesso_erros").addClass("error");
        $("#form_acesso_erros_msg").html(ret.msg);
        $("#form_acesso_erros").show();
    }
}