function vldField(field,required,type,evt){
    var msgRequired = "Campo obrigatório!";
    if (document.getElementById(field)!= null) {
        try {
            //Define em qual evento a validação deve ser disparada:
            if (evt == 'onSubmit') {
                if (type == 'FONE') {
                    var fld = new LiveValidation(field,{onValid: function(){return},onlyOnSubmit: true });   
                } else {            
                    var fld = new LiveValidation(field,{validMessage: 'Obrigado!', onlyOnSubmit: true });
                }
            } else if (evt == 'onBlur') {
                if (type == 'FONE') {
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
                fld.add( Validate.Length,{minimum: 4, maximum: 40,tooShortMessage:"O nome informado parece estar incorreto."});                                
            } else if (type == 'LOGIN') {
                fld.add( Validate.Length,{minimum: 10, maximum: 30,tooShortMessage:"O login deve ter no mínimo 10 caracteres."});                                
            } else if (type == 'PASSWORD') {
                fld.add( Validate.Length,{minimum: 8, maximum: 15,tooShortMessage:"A senha deve ter no mínimo 8 caracteres."});                                
            }
        } catch(e) {
            alert(e.message);
        }
    } else {
        alert('O campo '+field+' não existe.');        
    }    
}