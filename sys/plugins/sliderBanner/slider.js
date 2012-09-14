Slider = function(){};

Slider.prototype = {
    /*
    * Class of abstraction to Coin-Slider Jquery Plugin
    * 
    * @JSon Object opts
    * @String opts.id - HTML Object's ID
    * @Int opts.delay - Transiction time (miliseconds)
    * @String opts.effect - Transiction Type effect
    * @Int opts.width - Slider's width
    * @Int opts.height - Slider's height
    **/
    init: function(opts){
        try{
            if($.trim(opts.id) == ''){
                //If not ID defined - "Slider ID is not defined!"
                throw new Error(Dic.loadMsg("Slider", "ERROR_ID", "init"));
            }
            
            $('#' + opts.id).coinslider({ 
                navigation: true, 
                delay: opts.delay != null ? opts.delay : 5000,
                effect: opts.effect != null ? opts.effect : 'rain',
                hoverPause: true,
                width: opts.width != null ? opts.width : 900,
                height: opts.height != null ? opts.height : 400
            });
        }catch(err){
            alert(Dic.loadMsg("Slider", "CATCH", "init") + " " + err.message);
        }
    }
}


