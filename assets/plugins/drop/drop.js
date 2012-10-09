$(document).ready(function(){

    $(".drop ul li ul").hide();//aqui eu escondo as ul´s que forem filhas de li.
    $(".drop ul li").hover(function(){//O método hover recebe dois parametros que são duas funções.
        $(this).find("ul:first").slideDown("slow"); //aqui você faz o que quiser quando o mouse estiver em cima

    }, function(){
        $(this).find("ul:first").slideUp("slow"); //aqui é como se fosse o callback e você também faz o que quiser.
    });
});
