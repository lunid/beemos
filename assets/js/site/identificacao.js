$(document).ready(function() {

        //vldField('CONTATO_NOME','STRING','onBlur');
        vldField('CONTATO_EMAIL','EMAIL','onBlur');
        //vldField('RAZAO_SOCIAL','STRING','onBlur');
        //vldField('NOME_COMERCIAL','STRING','onBlur');
        //vldField('SITE','STRING','onBlur');
        
        //Telefones:
        vldField('CONTATO_FONE_FIXO','FONE','onBlur');
        //vldField('FONE_PABX','FONE','onBlur');  
        
        vldField('LOGIN','LOGIN','onBlur');
        vldField('PASSWORD','PASSWORD','onBlur');
        var fldPoliticas = vldField('ACEITO_POLITICAS','CHECKBOX','onSubmit');
        if (fldPoliticas != null) {
            fldPoliticas.add( Validate.Acceptance, {  value: true, failureMessage: "É necessário aceitar os Termos de Serviço e a Política de Privacidade para continuar." } );            
        }
        
        submitForm();
});


function submitForm(){
    $("#formIdentificacao").submit(function(event) {
 
      /* stop form from submitting normally */
      event.preventDefault();

      /* get some values from elements on the page: */
      var $form = $( this ),    
          dados = $form.serialize();
          url   = $form.attr( 'action' );

      /* Send the data using post */
      var posting = $.post( url, dados, function(){
        $( "#resultForm" ).empty().append( 'Enviando dados, aguarde...' );
      });

      /* Put the results in a div */
      posting.done(function( data ) {
        alert(data);
        var content = $( data ).find( '#content' );
        $( "#resultForm" ).empty().append( content );
      });
    });
}
