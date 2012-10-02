$(document).ready(function() {
    form = new Form();
    form.init('form_login_cadastro');
    form.init('form_esqueci_cadastro');
    form.init('form_novo_cadastro');
    form.initModal('cadastro');
});

function esquecerRedeSocial(rede){
    if(rede == 'fb'){
        $('#fb_id').val('');
        $('#esquecer_fb').css('display', 'none');
    }else if(rede == 'google'){
        $('#google_id').val('');
        $('#google_fb').css('display', 'none');
    }
    
    $('#email_cadastro').removeAttr('readonly');
    $('#email_cadastro').css('background-color', '#FFF');
}