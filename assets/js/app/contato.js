$(document).ready(function(){
    //Formulário de envio
    formContato = new Form();
    formContato.init("form_contato");
});

/**
 * Verifica retorno da operação de envio de e-mail 
 */
function verSalvarContato(ret, modalId){
    //Remove todas possíveis classes
    $("#form_contato_erros").removeClass("warning");
    $("#form_contato_erros").removeClass("success");
    $("#form_contato_erros").removeClass("error");
        
    if(ret.status){
        $("#form_contato_erros").addClass("success"); //Adiciona classe de sucesso
        $("#form_contato_erros_msg").html(ret.msg); //Adiciona mensagem
        $("#form_contato_erros").show(); //Exibe notificações
    }else{
        $("#form_contato_erros").addClass("error");
        $("#form_contato_erros_msg").html(ret.msg);
        $("#form_contato_erros").show();
    }
}