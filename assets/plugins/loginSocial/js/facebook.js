$(document).ready(function() {            
    //Ferramentas JS do Facebook
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;        
        var appId = '331964393598999';
        js.src = "//connect.facebook.net/pt_BR/all.js#xfbml=1&appId="+appId;
        fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));         
});


/**
 * Abre janela de dialogo com o Facebook Login
 */
function openWinFacebook() {
    var appId       = '331964393598999';
    var redirect    = "http://www.supervip.com.br/dev/auth/login/fb";
    window.location.href = "https://www.facebook.com/dialog/oauth?client_id="+appId+"&redirect_uri="+redirect+"&scope=email";
}
