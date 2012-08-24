/*
 * Classe de abstração para funções JavaScript da menu
 **/
$(document).ready(function () {
    $('img.menu_class').click(function () {
    $('ul.the_menu').slideToggle('medium');
    });
});