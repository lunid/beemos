/*
 * Classe de abstração para funções JavaScript da Home
 **/
Home = function(){};

Home.prototype = {
    init: function(){
        var slider = new Slider();
        slider.init({
            id: 'banner'
        });
        
        menu = new Menu();
        menu.init({
            id: 'top-menu'
        });
    },
    
    areaAluno: function(){
        try{
            $("#aba_aluno").removeClass('loginAbaInativa').addClass('loginAbaAtiva');   
            $("#aba_assinante").removeClass('loginAbaAtiva').addClass('loginAbaInativa');                           
            $("#codigoLista").css("display", 'block');
            $("#area_assinante").css("border-radius", "8px 8px 8px 8px");
        }catch(err){
            alert(Dic.loadMsg("Home", "CATCH", "changeArea") + " " + err.message);
        }
    },
    areaAssinante: function(){
        try{
            $("#aba_aluno").removeClass('loginAbaAtiva').addClass('loginAbaInativa');   
            $("#aba_assinante").removeClass('loginAbaInativa').addClass('loginAbaAtiva');                           
            $("#codigoLista").css("display", 'none');
            $("#area_assinante").css("border-radius", "0px 8px 8px 8px");
        }catch(err){
            alert(Dic.loadMsg("Home", "CATCH", "changeArea") + " " + err.message);
        }
    }    
};

 $(document).ready(function() {
    home = new Home();
    home.init();
    oHandler = $(".mydds").msDropDown().data("dd");
    $("#ver").html($.msDropDown.version);
}); 
