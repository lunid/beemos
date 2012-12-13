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
        sortorder: "DESC",
        caption:"Usuários",
        width: 900,
        height: 'auto',
        scrollOffset: 0
    });
                
    $("#grid_usuarios").filterToolbar();
    
    $("#grid_usuarios")
        .navGrid('#pg_usuarios',{edit:false,add:false,del:false,search:false})
        .navButtonAdd('#pg_usuarios',{
            caption: "Novo Usuário", 
            buttonicon: "ui-icon-plus", 
            onClickButton: function(){ 
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
    
    //Inicia formulário
    formUsuario = new Form();
    formUsuario.init('form_usuario');
});

/**
 * Altera status de bloqueio do usuário
 */
function bloquearUsuario(idMatriz, idCliente, status){
    site.aguarde();
    
    $.post(
        'bloquearUsuario',
        {
            idMatriz: idMatriz,
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
            alert("Falha no servidor! Entre em contato com o suporte.");
        }
    );
}

/**
 * Verifica a geração de senha manual ou automática
 */
function checkSenha(obj){
    //Limpa os campos de senha
    $("#PASSWD").val("");
    $("#C_PASSWD").val("");
        
    if(obj.checked){
        //Esconde campos de senha
        $("#senha").css("display", "none");
    }else{
        //Exibe campos de senha
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
    }else{
        $("#form_usuario_erros").addClass("error");
        $("#form_usuario_erros_msg").html(ret.msg);
        $("#form_usuario_erros").show();
    }
}