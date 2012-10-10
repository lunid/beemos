Dropdown = function(){};

Dropdown.prototype = {
    /*
    * Class of abstraction to Jquery DD Plugin
    **/
    init: function(){
        try{
            oHandler = $(".mydds").msDropDown().data("dd");
            $("#ver").html($.msDropDown.version);
        }catch(err){
            alert(Dic.loadMsg("Dropdown", "CATCH", "init") + " " + err.message);
        }
    }
};