$(document).ready(function() {
    $.post(
        "sys/dic/pt/javascript.xml",
        null,
        function(xml){
            xml_dic = xml;
            
            menu = new Menu();
            menu.init({
                id: 'top-menu'
            });

            dropDown = new Dropdown();
            dropDown.init();
        },
        'xml'
    );
});