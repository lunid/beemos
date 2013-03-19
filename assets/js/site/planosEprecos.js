$(document).ready(function() {
    $("#recursos li span[title]").tooltip({

    });
    
    $("span.question_mark").click(function(){
       $(this).css('cursor', 'pointer');
       alert('sdf');
    })
});