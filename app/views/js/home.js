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