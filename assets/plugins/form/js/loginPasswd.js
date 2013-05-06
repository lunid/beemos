
$(document).ready(function() {
    
    if (document.getElementById('LOGIN') != null) {
        //Impede a digitação de caracteres não aceitos para o campo de LOGIN.              
       $('#LOGIN').keypress(function(e){
           if(e.which != 0) {
                var regex = /[^A-Za-z0-9@.]/;        
                return testRegex(e,regex);
           }
       });
    }

    if (document.getElementById('PASSWORD') != null) {
        //Impede a digitação de caracteres não aceitos para o campo de SENHA.         
        $('#PASSWORD').keypress(function(e){
            if(e.which != 0) {
                var regex = /[^A-Za-z0-9]/;    
                return testRegex(e,regex);
            }
        });        
    }
});
