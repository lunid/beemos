Menu=function(){};Menu.prototype={init:function(opts){try{if($.trim(opts.id)==""){throw new Error(Dic.loadMsg("Menu","ERROR_ID_MENU"))}this.id=opts.id;$("#magic-line").remove();var $el,leftPos,newWidth;$("#"+opts.id).append("<li id='magic-line'></li>");var $magicLine=$("#magic-line");$magicLine.width($(".current_page_item").width()).css("left",$(".current_page_item a").position().left).data("origLeft",$magicLine.position().left).data("origWidth",$magicLine.width());$("#"+opts.id+" li").find("a").hover(function(){$el=$(this);leftPos=$el.position().left;newWidth=$el.parent().width();$magicLine.stop().animate({left:leftPos,width:newWidth})},function(){$magicLine.stop().animate({left:$magicLine.data("origLeft"),width:$magicLine.data("origWidth")})})}catch(err){alert(Dic.loadMsg("Menu","CATCH","init")+" "+err.message)}},changeMenu:function(id){try{if($.trim(this.id)==""){throw new Error(Dic.loadMsg("Menu","ERROR_ID_MENU"))}if($.trim(id)==""){throw new Error(Dic.loadMsg("Menu","ERROR_ID_ITEM","changeMenu"))
}var menu=$("#"+this.id);menu.each(function(){$(this).find("li").each(function(){$(this).removeAttr("class")})});$("#"+id).attr("class","current_page_item");this.init({id:this.id})}catch(err){alert(Dic.loadMsg("Menu","CATCH","changeMenu")+" "+err.message)}}};