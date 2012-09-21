Form = function(){};



Form.prototype = {

    init: function(form_id){

        try{

            if($.trim(form_id) == ''){

                //If not ID defined

                throw new Error(Dic.loadMsg("Form", "ERROR_ID", "init"));

            }

            

            $("#" + form_id + " .required").each(function(){

                $(this).after("<span id='msg_error_" + this.id + "' style='display:none;' class='msg_error'>" + Dic.loadMsg("Form", "FIELD_REQUIRED", "init").replace("%%NAME%%", $.trim($(this).attr('field_name'))) + "</span>");

                

                var tip = $.trim($(this).attr('tip'));

                

                if(tip != null && tip != ''){

                   $(this).qtip({

                        content: tip,

                        style: { 

                            name: 'cream', 

                            border: {

                                width: 1,

                                radius: 8

                            },

                            tip: { 

                                corner: 'topLeft' 

                            }

                        },

                        show: 'mouseover focusin',

                        hide: 'mouseout focusout'

                    });

                }

            });

            

            $("#" + form_id + " .email").each(function(){

                $(this).after("<span id='msg_error_email_" + this.id + "' style='display:none;' class='msg_error'>" + Dic.loadMsg("Form", "FIELD_EMAIL", "init") + "</span>");

            });

            

            $("#" + form_id + " .phone").each(function(){

                $(this).mask("(99)9999-9999?9");

            });

        }catch(err){

            alert(Dic.loadMsg("Form", "CATCH", "init") + " " + err.message);

        }

    },

    

    validate: function(form){

        try{

            var validate = true;

            

            $(form).find(".required").each(function(){

                if($.trim(this.value) == '' || this.value == null || parseInt(this.value) == 0){

                    $("#msg_error_" + this.id).show('normal');

                    this.focus();

                    validate = false;

                }else{

                    $("#msg_error_" + this.id).hide('fast');

                }

            });

            

            if(!validate){

                return false;

            }

            

            $(form).find(".email").each(function(){

                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

                

                if(!re.test($.trim(this.value))){

                    $("#msg_error_email_" + this.id).show('normal');

                    this.focus();

                    validate = false;

                }else{

                    $("#msg_error_email_" + this.id).hide('fast');

                }

            });

            

            if(validate){

                return true;

            }else{

                return false;

            }

        }catch(err){

            alert(Dic.loadMsg("Form", "CATCH", "validate") + " " + err.message);

            return false;

        }

    },

    

    validateEmail: function(email) { 

        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        return re.test(email);

    } 

}





