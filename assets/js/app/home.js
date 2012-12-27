//Banner rotativo
$(document).ready(function() {
    $('.pix_diapo').diapo();
    
    //Formulário experimente
    formExperimente = new Form();
    formExperimente.init("form_experimente");
});

/**
 * Verifica retorno da operação de cadastro de visitantes
 */
function verSalvarVisitante(ret, modalId){
    alert(ret.msg);
}