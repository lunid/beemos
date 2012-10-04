/* 
 * Classe para funções utilizadas em todo site
 */
Site = function(){};

Site.prototype = {
    verAvaliacao: function(ret){
        if(ret.status == true){
            $('#bt_submit').hide();
        }
    }
};