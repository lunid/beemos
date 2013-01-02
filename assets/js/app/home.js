//Banner rotativo
$(document).ready(function() {
    $('.pix_diapo').diapo();
    
    //Formulário Login
    formLogin = new Form();
    formLogin.init("form_login");
});

/**
 * Verifica retorno da operação de cadastro de visitantes
 */
function validaLogin(ret, modalId){
    alert(ret.msg);
}