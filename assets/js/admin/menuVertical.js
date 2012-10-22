/*
 * MENU VERTICAL
 * Script que controla a aparência dos botões com css
 */ 
 
$(document).ready(function() {
    $("ul.sub-menu li a").click(function() { //When trigger is clicked...
            $(this).hover(function() { 
                    $(this).addClass("subhover"); //On hover over, add class "subhover"
            }, function(){	//On Hover Out
                    $(this).removeClass("subhover"); //On hover out, remove class "subhover"
            });
    });
});