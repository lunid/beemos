$(document).ready(function() {
    vldField('NEWSLETTER','CAD_NEWSLETTER','onBlur');
    //Rollover de imagens:
    $("img.rollover").hover(function(){
        $(this).stop().animate({"opacity": ".5"}, "fast").attr('src', this.src.replace("_off","_on")).animate({"opacity": "1"}, "fast");
    }, function() {
        $(this).stop().attr('src', this.src.replace("_on","_off")).animate({"opacity": "1"}, "fast");        
    });
});
