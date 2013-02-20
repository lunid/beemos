$(document).ready(function() {
    var fldNome     = new LiveValidation('CONTATO_NOME',{validMessage: 'Obrigado!'},{onlyOnSubmit: true } );
    var fldEmail    = new LiveValidation('CONTATO_EMAIL',{validMessage: 'Obrigado!',onlyOnBlur: true } );
    
    fldNome.add( Validate.Presence, { failureMessage: "Campo obrigatório." } );   
    fldEmail.add( Validate.Presence, { failureMessage: "Campo obrigatório." } );   
    fldEmail.add(Validate.Email, { failureMessage: "E-mail incorreto."});
       
});