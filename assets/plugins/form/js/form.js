$(document).ready(function() {
    //Caso os campos PASSWD e PASSWD_CONF existam, faz a comparação do campo de confirmação com o campo PASSWD
    if (document.getElementById('PASSWORD') != null && document.getElementById('PASSWORD_CONF') != null) {  
        vldField('PASSWORD_CONF','PASSWORD_CONF','onBlur');        
    }
});

function vldField(field,type,evt){
    var msgRequired = "Campo obrigatório!";
    var fld         = null;
    
    if (document.getElementById(field)!= null) {
        try {
            //Define em qual evento a validação deve ser disparada:
            var required = $('#'+field).hasClass("required");//Verifica se o campo é obrigatório.
            
            if (evt == 'onSubmit') {
                if (type == 'FONE' || type == 'PASSWORD_CONF' || type == 'CHECKBOX') {
                    var fld = new LiveValidation(field,{onValid: function(){return},onlyOnSubmit: true });   
                } else {            
                    var fld = new LiveValidation(field,{validMessage: 'Obrigado!', onlyOnSubmit: true });
                }
            } else if (evt == 'onBlur') {
                if (type == 'FONE' || type == 'PASSWORD_CONF') {
                    var fld = new LiveValidation(field,{onValid: function(){return},onlyOnBlur: true });   
                } else {
                    var fld = new LiveValidation(field,{validMessage: 'Obrigado!', onlyOnBlur: true });
                }
            }

            //Verifica se o campo deve ser obrigatório:
            if (required == true) fld.add( Validate.Presence, { failureMessage: msgRequired } ); 

            //Faz validações específicas para cada tipo:
            if (type == 'EMAIL') {
                fld.add(Validate.Email, { failureMessage: "E-mail incorreto."} );
            } else if (type == 'STRING') {
                fld.add( Validate.Length,{minimum: 4, maximum: 40,tooShortMessage:"O valor informado é muito curto."});                                
            } else if (type == 'LOGIN') {
                fld.add( Validate.Length,{minimum: 10, maximum: 30,tooShortMessage:"O login deve ter no mínimo 10 caracteres."});                                
            } else if (type == 'PASSWORD') {
                fld.add( Validate.Length,{minimum: 8, maximum: 15,tooShortMessage:"A senha deve ter no mínimo 8 caracteres."});                                
            } else if (type == 'PASSWORD_CONF') {
                fld.add( Validate.Confirmation, { match: 'PASSWORD' , failureMessage: "A senha não combina." } );
            }
            return fld;
        } catch(e) {
            alert(e.message);
        }
    } else {
        alert('O campo '+field+' não existe.');        
    }    
}