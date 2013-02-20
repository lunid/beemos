/**
 * Função para validação do digito 9 para celulares
 */
function celularMask(id) {    
    $('#'+id).focusout(function(){
        var phone, element;
        element = $(this);
        element.unmask();
        phone = element.val().replace(/\D/g, '');
        if(phone.length > 10) {
            element.mask("(99) 99999-999?9");
        } else {
            element.mask("(99) 9999-9999?9");
        }
    }).trigger('focusout');    
}

jQuery('#TELEFONE').keyup(function(){
    var id = '#TELEFONE';
    if( jQuery(id).hasClass('celularsp'))
        return;

    var cel = jQuery('#TELEFONE').val().substring(1, 3);

    if( cel == '11' ) {        
            jQuery(id).removeClass('telefone');
            jQuery(id).addClass('celularsp');
            jQuery(id).unmask(); 
            jQuery(id).mask("(99) 99999-9999", [], "(11) _____-____");

            var elem = document.getElementById('telefone');
                if(elem != null) {
                    if(elem.createTextRange) {
                        var range = elem.createTextRange();
                        range.move('character', 5);
                        range.select();
                    }
                    else {
                        if(elem.selectionStart) {
                            elem.focus();
                            elem.setSelectionRange(5, 5);
                        }
                        else
                            elem.focus();
                    }
                }
    }
});
jQuery(document).ready(function() {
    jQuery("input.TELEFONE").mask("(99) 9999-9999");
    jQuery("input.celularsp").mask("(99) 99999-9999");
});