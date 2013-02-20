
/**
 * Author: Franklin Javier Gonzalez
 * Date: 04/01/2010
 * Version: 1.0a
 *
 * Adapted by Paulo de Tarso - Added option to limit characters via parameter
 * 
 * If you use this script, please link back to the source
 *
 * Please report any bug at contato@franklinjavier.com
 * Copyright (c) 2010 Franklin Javier http://www.franklinjavier.com
 *
 * Released under the Creative Commons Attribution 3.0 Unported License,
 * as defined here: http://creativecommons.org/licenses/by/3.0/
 *  
 */
jQuery( function( $ )
{
        $.fn.extend(
        {
                maxlength: function( options )
                {

                        var defaults = {

                                limit : 250
                
                        },
                        options = $.extend( defaults, options ),
                        $this = $( this );


                        $this.live( 'keyup keypress keydown change mouseover', function( event )
                        {
        
                                var limit = options.limit,
                                        length = $this.val().length,
                                        key,
                                        ie = (typeof window.ActiveXObject != 'undefined'); // IE

                                (ie) ? key = event.keyCode : key = event.which; // IE (keyCode), else, (wich)


                                if((key >= 48 && key <= 112) || key == 13 || key == 32)
                                        if (length >= limit) 
                                                event.preventDefault();
                                                        
                                if(length > limit)      
                                        $this.val($this.val().substring(0, limit));
        
                        });

                }

        });

});

$("textarea").textarea_maxlength();