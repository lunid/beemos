xml_dic = null;

Dic = function(){};

Dic.loadMsg = function(class_name, msg_param, method){
    try{
        if($.trim(method) == '' || method == null){
            method = 'default';
        }
        var xml_class   = $(xml_dic).find(class_name);
        var xml_method  = $(xml_class).find(method);
        var msg = xml_method.find('msg[id="' + msg_param + '"]');
        return msg.text();
    }catch(err){
        alert("Error to load message in dictionary: " + err.message);
    }
}