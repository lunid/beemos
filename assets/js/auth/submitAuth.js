function checkAuth(form){
    var user    = $('#user').val();
    var passwd  = $('#passwd').val();
    var token   = $('#token').val();   
    if (user.length > 0 && passwd.length > 0) {        
       form.submit();
    } else {
        msg = "Por favor, preencha corretamente os campos abaixo:\n\n";
        if (user.length == 0) msg += " - Usuário\n";
        if (passwd.length == 0) msg += " - Senha\n";
        alert(msg);
    }    
}