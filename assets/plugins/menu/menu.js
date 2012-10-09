Menu = function(){};

Menu.prototype = {
    /*
    * Class of abstraction to Magic-line Jquery Plugin
    * 
    * @JSon Object opts
    * @String opts.id - HTML Object's ID
    * 
    **/
    init: function(opts){
        try{
            if($.trim(opts.id) == ''){
                //If not ID defined
                throw new Error(Dic.loadMsg("Menu", "ERROR_ID_MENU"));
            }

            this.id = opts.id;
            $("#magic-line").remove();
            var $el, leftPos, newWidth;

            /* Add Magic Line markup via JavaScript, because it ain't gonna work without */
            $("#" + opts.id).append("<li id='magic-line'></li>");

            /* Cache it */
            var $magicLine = $("#magic-line");

            $magicLine.width($(".current_page_item").width()).css("left", $(".current_page_item a").position().left).data("origLeft", $magicLine.position().left).data("origWidth", $magicLine.width());

            $("#" + opts.id + " li").find("a").hover(function() {
                $el         = $(this);
                leftPos     = $el.position().left;
                newWidth    = $el.parent().width();

                $magicLine.stop().animate({
                    left: leftPos,
                    width: newWidth
                });
            }, 
            function() {
                $magicLine.stop().animate({
                    left: $magicLine.data("origLeft"),
                    width: $magicLine.data("origWidth")
                });    
            });
        }catch(err){
            alert(Dic.loadMsg("Menu", "CATCH", "init") + " " + err.message);
        }
    },
    
    /*
    * Method to change menu option
    * 
    * @String id - ID of new menu option selected
    * 
    **/
    changeMenu: function(id){
        try{
            if($.trim(this.id) == ''){
                //If not ID defined
                throw new Error(Dic.loadMsg("Menu", "ERROR_ID_MENU"));
            }

            if($.trim(id) == ''){
                //If not ID defined
                throw new Error(Dic.loadMsg("Menu", "ERROR_ID_ITEM", "changeMenu"));
            }

            var menu = $("#" + this.id);

            menu.each(function(){
                $(this).find('li').each(function(){
                    $(this).removeAttr('class');
                });
            });

            $("#" + id).attr("class", "current_page_item");

            this.init({id: this.id});
        }catch(err){
            alert(Dic.loadMsg("Menu", "CATCH", "changeMenu") + " " + err.message);
        }
    }
}