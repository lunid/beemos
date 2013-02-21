$(document).ready(function() {

        vldField('CONTATO_NOME',true,'STRING','onBlur');
        vldField('CONTATO_EMAIL',true,'EMAIL','onBlur');
        vldField('SITE',true,'STRING','onBlur');
        
        //Telefones:
        vldField('CONTATO_FONE_FIXO',true,'FONE','onBlur');
        vldField('FONE_PABX',true,'FONE','onBlur');  
        
        vldField('LOGIN',true,'LOGIN','onBlur');
        vldField('PASSWD',true,'PASSWORD','onBlur');
        
        
});

