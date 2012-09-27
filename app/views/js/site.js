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
    }
}

$(document).ready(function() {
    site = new Site();
});         