$(document).ready(function() {
    $.ajax({
        url: "sys/dic/pt/javascript.xml",
        success: function(xml){
            xml_dic = xml;
        },
        async: false
    });
});