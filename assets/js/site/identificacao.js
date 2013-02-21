$(document).ready(function() {
    try {
        var msgRequired     = "Campo obrigatório!";

        var fldNome = new LiveValidation('CONTATO_NOME',{validMessage: 'Obrigado!', onlyOnSubmit: true });
        fldNome.add( Validate.Presence, { failureMessage: msgRequired } );  
        fldNome.add( Validate.Length,{minimum: 4, maximum: 40,tooShortMessage:"Por favor, informe o nome completo."});
        
        var fldEmail = new LiveValidation('CONTATO_EMAIL',{validMessage: 'Obrigado!', onlyOnBlur: true });
        fldEmail.add( Validate.Presence, { failureMessage: msgRequired } );   
        fldEmail.add(Validate.Email, { failureMessage: "E-mail incorreto."} );
        
        var fldRazaoSocial  = new LiveValidation('RAZAO_SOCIAL',{validMessage: 'Obrigado!', onlyOnBlur: true });
        fldRazaoSocial.add( Validate.Presence, { failureMessage: msgRequired } );  
               
        var fldFoneContato  = new LiveValidation('CONTATO_FONE_FIXO',{onValid: function(){return},onlyOnBlur: true });
        fldFoneContato.add( Validate.Presence, { failureMessage: msgRequired } );  
                
        var fldFonePabx     = new LiveValidation('FONE_PABX',{onValid: function(){return},onlyOnBlur: true });
        fldFonePabx.add( Validate.Presence, { failureMessage: msgRequired } );              
    } catch(e){
        alert(e.message);
    }
    $('#CONTATO_FONE_FIXO').mask("(99) 9999-9999");
    $('#FONE_PABX').mask("(99) 9999-9999");
    $('#CONTATO_CELULAR').mask("(99) 9999-9999?9");
});