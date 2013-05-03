$(document).ready(function() {
    if (document.getElementById('NEWSLETTER') != null) {
        vldField('NEWSLETTER','CAD_NEWSLETTER','onBlur');
    }
    //Rollover de imagens:
    $("img.rollover").hover(function(){
        $(this).stop().animate({"opacity": ".5"}, "fast").attr('src', this.src.replace("_off","_on")).animate({"opacity": "1"}, "fast");
    }, function() {
        $(this).stop().attr('src', this.src.replace("_on","_off")).animate({"opacity": "1"}, "fast");        
    });
    
    /**
     * Torna clicável a div com a class 'click'.
     * Acrescenta o pointeiro do mouse 'pointer' e redireciona o usuário para o link contido
     * na tag A, dentro da div.
     */
    $(".btnClick").click(function() {
       objTagA  = $(this).find('a');
       href_    = objTagA.attr("href");
       target_  = objTagA.attr("target");

       if (href_ != 'undefined' || href_.length > 0) {
           window.location.href = href_;
       }
    }).css("cursor","pointer");
});
