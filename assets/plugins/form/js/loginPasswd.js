
/**
 * Impede a digitação de caracteres não aceitos.
 * Implementação: onKeyPress="return login(event);"
 */
function login(e){
    var regex = /[^A-Za-z0-9@.]/;        
    return testRegex(e,regex);
}

/**
 * Impede a digitação de caracteres não aceitos.
 * Implementação: onKeyPress="return password(event);"
 */
function password(e){
    var regex = /[^A-Za-z0-9]/;    
    return testRegex(e,regex);
}

function testRegex(e,regex){
    var key;
    if (e.keyCode) key = e.keyCode;
    else if (e.which) key = e.which;    
    if (regex.test(String.fromCharCode(key)) || key == 32) return false;   
    return true;    
}