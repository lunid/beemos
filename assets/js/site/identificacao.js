$(document).ready(function() {

        vldField('CONTATO_NOME','STRING','onBlur');
        vldField('CONTATO_EMAIL','EMAIL','onBlur');
        vldField('RAZAO_SOCIAL','STRING','onBlur');
        vldField('NOME_COMERCIAL','STRING','onBlur');
        vldField('SITE','STRING','onBlur');
        
        //Telefones:
        vldField('CONTATO_FONE_FIXO','FONE','onBlur');
        vldField('FONE_PABX','FONE','onBlur');  
        
        vldField('LOGIN','LOGIN','onBlur');
        vldField('PASSWORD','PASSWORD','onBlur');
        var fldPoliticas = vldField('ACEITO_POLITICAS','CHECKBOX','onSubmit');
        if (fldPoliticas != null) {
            fldPoliticas.add( Validate.Acceptance, {  value: true, failureMessage: "É necessário aceitar os Termos de Serviço e a Política de Privacidade para continuar." } );            
        }

});

