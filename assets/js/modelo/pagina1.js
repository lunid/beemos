//A classe abaixo deve ser incluída na tag <ul> da página que possui o link
$('.list li').click(function() {
    var launch = $('a.launch', this);
    if (launch.size() > 0) { this.onclick = launch.attr('onclick'); }
});