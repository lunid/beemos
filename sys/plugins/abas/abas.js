function tabSwitch(new_tab, new_content) {
    $(".abas").each(function(){
       $(this).removeClass('active');
    });
    
    $(".content").each(function(){
       $(this).css('display', 'none');        
    });
    
    $('#' + new_tab).addClass('active');
    $('#' + new_content).css('display', 'block');
}