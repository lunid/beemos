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
    }      
};

$(document).ready(function() {
    home = new Home();
    home.init();    
});


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
    }
};

$(document).ready(function() {
    home = new Home();
    home.init();

    oHandler = $(".mydds").msDropDown().data("dd");
    $("#ver").html($.msDropDown.version);
});