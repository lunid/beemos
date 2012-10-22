$(document).ready(function() {
    $.ajax({
        url: "sys/dic/pt/javascript.xml",
        success: function(xml){
            xml_dic = xml;
            
            //Inicializa objeto com funções do site
            //site = new Site();
        },
        async: false
    });
});

/* 
 * Classe para funções utilizadas em todo site
 */
Site = function(){};

Site.prototype = {
    verAvaliacao: function(ret, modalId){
        if(ret.status == true){
            $('#bt_submit').hide();
            //Exibe msg de retorno ao user
            if(modalId != "" && modalId != null){
                $.fancybox.close(true);
                
                $("#msg_" + modalId).html(ret.msg);
                $("#modal_" + modalId).trigger('click');
            }
        }
    }
};