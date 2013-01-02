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