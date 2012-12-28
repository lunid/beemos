$(document).ready(function() {
    $.ajax({
        url: "sys/dic/pt/javascript.xml",
        success: function(xml){
            xml_dic = xml;
            
            //Inicializa objeto com funções do site
            site = new Site();
            
            //Ferramentas JS do Facebook
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/pt_BR/all.js#xfbml=1&appId=470110013021201";
                fjs.parentNode.insertBefore(js, fjs);
              }(document, 'script', 'facebook-jssdk')); 
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
    }
};