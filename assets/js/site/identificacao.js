$(document).ready(function() {

        vldField('CONTATO_NOME','STRING','onBlur');
        vldField('CONTATO_EMAIL','EMAIL','onBlur');
        vldField('SITE','STRING','onBlur');
        
        //Telefones:
        vldField('CONTATO_FONE_FIXO','FONE','onBlur');
        vldField('FONE_PABX','FONE','onBlur');  
        
        vldField('LOGIN','LOGIN','onBlur');
        vldField('PASSWORD','PASSWORD','onBlur');

});

