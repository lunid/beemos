$(document).ready(function() {
    $.ajax({
        url: "/interbits/sys/dic/pt/javascript.xml",
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
            beforeClose: function( event, ui ) { $(".ui-dialog-titlebar").show(); },
            width: '205',
            height: '115',
            zIndex: 9999
        });
    },
    fechaAguarde: function(){
        $( "#modal_aguarde" ).dialog("close");
    },
    modal: function(msg, title, cssClass, buttons, width){
        //Seta largura padrão
        if(width <= 0 || width == null){
            width = 350;
        }
        
        //Seta mensagem
        $("#msg_modal_padrao").html(msg);
        
        //Seta classe
        if(cssClass != null && cssClass != ''){
            $("#msg_modal_padrao").addClass(cssClass);
        }else{
            $("#msg_modal_padrao").attr('class', '');
        }        
        
        //Abre o modal
        $( "#modal_padrao" ).dialog({
            title: title,
            open: title != null && title != '' ? function(event, ui) { $(".ui-dialog-titlebar").show(); } : null,
            resizable: false,
            draggable: false,
            width: width,
            modal: true,
            zIndex: 9999,
            buttons: buttons != null ? buttons : null
        });
    },
    fechaModal: function(){
        //Zera mensagem
        $("#msg_modal_padrao").html('');
        //Zera css
        $("#msg_modal_padrao").attr('class', '');
        //Fecha modal
        $( "#modal_padrao" ).dialog("close");
    },
    formataGrid: function(rowId, tv, rawObject, cm, rdata) {
        var border = " border-left: 1px solid #D3D3D3 ";
        
        if(rowId == 0){
            return " colspan='11' style='color:#FF0000;font-weight:bold;" + border + "'";
        }else{
            return " style='" + border + "'";
        }
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