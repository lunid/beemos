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
    
    changeArea: function(id){
        try{
            if(id == null || $.trim(id) == ''){
                //If not ID defined
                throw new Error(Dic.loadMsg("Home", "ERROR_ID_AREA"));
            }
            
            $("#area_assinante").css("display", "none");
            $("#area_aluno").css("display", "none");
            
            $("#aba_assinante").attr("class", "assinante_inativa");
            $("#aba_aluno").attr("class", "aluno_inativa");
            
            $("#area_" + id).css("display", "");
            $("#aba_" + id).attr("class", id + "_ativa");
        }catch(err){
            alert(Dic.loadMsg("Home", "CATCH", "changeArea") + " " + err.message);
        }
    }
};