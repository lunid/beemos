xml_dic=null;$.post("dic/en/javascript.xml",null,function(xml){xml_dic=xml},"xml");Dic=function(){};Dic.loadMsg=function(class_name,msg_param,method){try{if($.trim(method)==""||method==null){method="default"}var xml_class=$(xml_dic).find(class_name);var xml_method=$(xml_class).find(method);var msg=xml_method.find('msg[id="'+msg_param+'"]');return msg.text()}catch(err){alert("Error to load message in dictionary: "+err.message)}};Home=function(){};Home.prototype={init:function(){var slider=new Slider();slider.init({id:"banner"});menu=new Menu();menu.init({id:"top-menu"})},changeArea:function(id){try{if(id==null||$.trim(id)==""){throw new Error(Dic.loadMsg("Home","ERROR_ID_AREA"))}$("#area_assinante").css("display","none");$("#area_aluno").css("display","none");$("#aba_assinante").attr("class","assinante_inativa");$("#aba_aluno").attr("class","aluno_inativa");$("#area_"+id).css("display","");$("#aba_"+id).attr("class",id+"_ativa")}catch(err){alert(Dic.loadMsg("Home","CATCH","changeArea")+" "+err.message)
}}};$(document).ready(function(){home=new Home();home.init();oHandler=$(".mydds").msDropDown().data("dd");$("#ver").html($.msDropDown.version)});