
/*---------------------------------
        Slideshow
-----------------------------------*/
// setup
$('ul.slideshow').wrap('<div class="slideshow-wrap"><div class="slideshow-inner"></div></div>')
.each(function(){
        var wrap = $(this).parents('.slideshow-wrap');
        var inner = $(this).parents('.slideshow-inner');

        // set height and width
        var swidth = $(this).attr('width');
        var sheight = $(this).attr('height');
        if(swidth != undefined && sheight != undefined){wrap.width(swidth); inner.height(sheight);}
        $(this).width('999em').attr('width','').attr('height','');

        $(this).find('li:first').addClass('current');
        $(this).delay(10000).animate({alpha:1}, function(){
                KSslideshow($(this), null);
        });
});