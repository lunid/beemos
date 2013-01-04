$(document).ready(function(){
    //Monta Formulário Login
    formLogin = new Form();
    formLogin.init("form_login");
    
    //Monta Formulário PF
    formPF = new Form();
    formPF.init("form_pf");
    
    //Máscara CPF
    $("#PF_CPF").mask("999.999.999-99");
    
    //Máscara Telefones
    $("#PF_DDD_TEL_RES").css("text-align", "center");
    $("#PF_DDD_TEL_RES").mask("99");
    
    $("#PF_TEL_RES").css("text-align", "center");
    $("#PF_TEL_RES").mask("9999-9999");
    
    $("#PF_DDD_CELULAR").css("text-align", "center");
    $("#PF_DDD_CELULAR").mask("99");
    
    $("#PF_CELULAR").css("text-align", "center");
    $("#PF_CELULAR").mask("9999-9999?9");
    
    $("#PF_DDD_TEL_COM").css("text-align", "center");
    $("#PF_DDD_TEL_COM").mask("99");
    
    $("#PF_TEL_COM").css("text-align", "center");
    $("#PF_TEL_COM").mask("9999-9999");
    
    //Máscara CEP
    $("#PF_CEP").mask("99999-999");
    
    //Máscara DT Nascimento
    $("#PF_DT_NASCIMENTO").mask("99/99/9999");
    
    //Data de nascimento
    $("#PF_DT_NASCIMENTO").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: '1900:2013',
        showOn: "both",
        buttonImageOnly: true, 
        buttonImage: '/interbits/assets/images/icon-calendar.png'
    });
    
    //Monta Formulário PJ
    formPJ = new Form();
    formPJ.init("form_pj");
    
    //Máscara CPF
    $("#PJ_CPF_CNPJ").mask("99.999.999/9999-99");
    
    //Máscara Telefones
    $("#PJ_DDD_TEL_COM").css("text-align", "center");
    $("#PJ_DDD_TEL_COM").mask("99");
    
    $("#PJ_TEL_COM").css("text-align", "center");
    $("#PJ_TEL_COM").mask("9999-9999");
    
    $("#PJ_DDD_TEL_2").css("text-align", "center");
    $("#PJ_DDD_TEL_2").mask("99");
    
    $("#PJ_TEL_2").css("text-align", "center");
    $("#PJ_TEL_2").mask("9999-9999");
    
    $("#PJ_DDD_CELULAR").css("text-align", "center");
    $("#PJ_DDD_CELULAR").mask("99");
    
    $("#PJ_CELULAR").css("text-align", "center");
    $("#PJ_CELULAR").mask("9999-9999?9");
    
    $("#PJ_DDD_FAX").css("text-align", "center");
    $("#PJ_DDD_FAX").mask("99");
    
    $("#PJ_FAX").css("text-align", "center");
    $("#PJ_FAX").mask("9999-9999");
    
    //Máscara CEP
    $("#PJ_CEP").mask("99999-999");
    
    //Máscara DT Nascimento
    $("#PJ_DT_NASCIMENTO").mask("99/99/9999");
    
    //Data de nascimento
    $("#PJ_DT_NASCIMENTO").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: '1900:2013',
        showOn: "both",
        buttonImageOnly: true, 
        buttonImage: '/interbits/assets/images/icon-calendar.png'
    });
});

/**
 * Pula para o passo de identificação
 */
function identificacao() {
    //Pega valor do campo Hidden
    var idPlano = parseInt($("#idPlano").val());
    
    //Valida Plano
    if(idPlano <= 0 || idPlano == null || idPlano == undefined){
        alert("Selecione um plano para continuar");
        return false;
    }
    
    //Redireciona o usuário
    window.location.href = "/interbits/planos/identificacao/?plano=" + idPlano;
}

/**
 * Quando o usuário seleciona um plano o idPlano é armazenado
 */
function selPlano(id) {
    $("#idPlano").val(parseInt(id));
    
    identificacao();
}

/**
 * Exibe formulário de cadastro caso seja um novo usuário
 */
function novoUsuario() {
    //Exibe cadastro
    $('#cadastro').css('display', '');
    
    //Rola pagina até o formulário de cadastro
    $('html, body').animate({
        scrollTop: $("#cadastro").offset().top
    }, 1500);
}

/**
 * Seleciona tipo de pessoa, Física ou Jurídica
 */
function selTipoPessoa(tipo) {
    if(tipo == 'PF'){
        $("#formPF").css("display", "");
        $("#formPJ").css("display", "none");
    }else if(tipo == 'PJ'){
        $("#formPJ").css("display", "");
        $("#formPF").css("display", "none");
    }

}

/**
 * Verifica retorno da operação de cadastro de Usuários PF
 */
function verSalvarUsuarioPF(ret, modalId){
    //Remove todas possíveis classes
    $("#form_pf_erros").removeClass("warning success error");
            
    if(ret.status){
        $("#form_pf_erros").addClass("success"); //Adiciona classe de sucesso
        $("#form_pf_erros_msg").html(ret.msg); //Adiciona mensagem
        $("#form_pf_erros").show(); //Exibe notificações
    }else{
        $("#form_pf_erros").addClass("error");
        $("#form_pf_erros_msg").html(ret.msg);
        $("#form_pf_erros").show();
    }
}

/**
 * Verifica retorno da operação de cadastro de Usuários PJ
 */
function verSalvarUsuarioPJ(ret, modalId){
    //Remove todas possíveis classes
    $("#form_pj_erros").removeClass("warning success error");
            
    if(ret.status){
        $("#form_pj_erros").addClass("success"); //Adiciona classe de sucesso
        $("#form_pj_erros_msg").html(ret.msg); //Adiciona mensagem
        $("#form_pj_erros").show(); //Exibe notificações
    }else{
        $("#form_pj_erros").addClass("error");
        $("#form_pj_erros_msg").html(ret.msg);
        $("#form_pj_erros").show();
    }
}