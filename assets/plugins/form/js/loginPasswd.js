
$(document).ready(function() {
    
    if (document.getElementById('LOGIN') != null) {
        //Impede a digitação de caracteres não aceitos para o campo de LOGIN.              
       $('#LOGIN').keypress(function(e){
           var regex = /[^A-Za-z0-9@.]/;        
           return testRegex(e,regex);
       });
    }

    if (document.getElementById('PASSWORD') != null) {
        //Impede a digitação de caracteres não aceitos para o campo de SENHA.          
        $('#PASSWORD').keypress(function(e){
            var regex = /[^A-Za-z0-9]/;    
            return testRegex(e,regex);
        });
    }
});

function testRegex(e,regex){
    var key;
    if (e.keyCode) key = e.keyCode;
    else if (e.which) key = e.which;    
    if (regex.test(String.fromCharCode(key)) || key == 32) return false;   
    return true;    
}