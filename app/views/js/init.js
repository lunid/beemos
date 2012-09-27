$(document).ready(function() {
    $.ajax({
        url: "sys/dic/pt/javascript.xml",
        success: function(xml){
            xml_dic = xml;

            menu = new Menu();
            menu.init({
                id: 'top-menu'
            });

            dropDown = new Dropdown();
            dropDown.init();
        },
        async: false
    });
});