$(document).ready(function() {
    $.ajax({
        url: "sys/dic/pt/javascript.xml",
        success: function(xml){
            xml_dic = xml;
            
            //Inicializa objeto com funções do site
            site = new Site();
        },
        async: false
    });
});

/* 
 * Classe para funções utilizadas em todo site
 */
Site = function(){};

Site.prototype = {
    aguarde: function(){
        $( "#modal_aguarde" ).dialog({
            resizable: false,
            draggable: false,
            modal: true,
            open: function(event, ui) { $(".ui-dialog-titlebar").hide(); },
            width: '190',
            height: '100',
            zIndex: 9999
        });
    },
    fechaAguarde: function(){
        $( "#modal_aguarde" ).dialog("close");
    },
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