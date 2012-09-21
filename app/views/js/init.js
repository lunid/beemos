$(document).ready(function() {
    var slider = new Slider();
    slider.init({
       id: 'banner'
    });
    alert('d');

    menu = new Menu();
    menu.init({
        id: 'top-menu'
    });
    
    oHandler = $(".mydds").msDropDown().data("dd");
    $("#ver").html($.msDropDown.version);
}); 