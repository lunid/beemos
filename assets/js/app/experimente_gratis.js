//Banner rotativo
$(document).ready(function() {
    //Formulário Visitante
    formVisitante = new Form();
    formVisitante.init("form_visitante");
    
    //Máscara para campo celular
    celularMask($("#celular"));
});

/**
 * Verifica retorno da operação de cadastro de visitantes
 */
function verSalvarVisitante(ret, modalId){
    //Remove todas possíveis classes
    $("#form_visitante_erros").removeClass("warning success error");
        
    if(ret.status){
        $("#form_visitante_erros").addClass("success"); //Adiciona classe de sucesso
        $("#form_visitante_erros_msg").html(ret.msg); //Adiciona mensagem
        $("#form_visitante_erros").show(); //Exibe notificações
    }else{
        $("#form_visitante_erros").addClass("error");
        $("#form_visitante_erros_msg").html(ret.msg);
        $("#form_visitante_erros").show();
    }
}