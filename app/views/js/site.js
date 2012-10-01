/* 
 * Classe para funções utilizadas em todo site
 */
Site = function(){};

Site.prototype = {
    areaAluno: function(){
        try{
            $("#aba_aluno").removeClass('loginAbaInativa').addClass('loginAbaAtiva');   
            $("#aba_assinante").removeClass('loginAbaAtiva').addClass('loginAbaInativa');                           
            $("#codigoLista").css("display", 'block');
            $("#area_assinante").css("border-radius", "8px 8px 8px 8px");
        }catch(err){
            alert(Dic.loadMsg("Site", "CATCH", "changeArea") + " " + err.message);
        }
    },
    areaAssinante: function(){
        try{
            $("#aba_aluno").removeClass('loginAbaAtiva').addClass('loginAbaInativa');   
            $("#aba_assinante").removeClass('loginAbaInativa').addClass('loginAbaAtiva');                           
            $("#codigoLista").css("display", 'none');
            $("#area_assinante").css("border-radius", "0px 8px 8px 8px");
        }catch(err){
            alert(Dic.loadMsg("Site", "CATCH", "changeArea") + " " + err.message);
        }
    },
    alternaLogin: function(local){
        try{
            if(local == 'aluno'){
                $('#box_login').hide();
                $('#box_aluno').show();
            }else{
                $('#box_aluno').hide();
                $('#box_login').show();
            }
        }catch(err){
            alert(Dic.loadMsg("Site", "CATCH", "alternaLogin") + " " + err.message);
        }
    },
    reloadPage: function (){
        window.location.reload();
    },
    logoff: function (){
        try{
            $("#modal_aguarde").trigger('click');

            $.post(
                'usuario/sair',
                null,
                function(ret){
                    if(ret.status){
                        window.location.reload();
                    }else{
                        $.fancybox.close(true);

                        alert(ret.msg);
                    }
                },
                'json'
            );
        }catch(err){
            alert(Dic.loadMsg("Site", "CATCH", "logoff") + " " + err.message);
        }
    },
    boxUsuario: function(){
        try{
            if($('#box_usuario').css('display') == 'none'){
                $('#box_usuario').show();
            }else{
                $('#box_usuario').hide();
            }
        }catch(err){
            alert(Dic.loadMsg("Site", "CATCH", "boxUsuario") + " " + err.message);
        }
    }
}

$(document).ready(function() {
    site = new Site();
});         