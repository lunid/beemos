$(document).ready(function() {
    form = new Form();
    form.init('form_avaliacao');
    form.initModal('top10_avaliar');
});

function selecionaNota(id){
    var dados = id.split("_");

    for(var i=1; i<=5; i++){
        $("#" + dados[0] + "_" + i).attr("src", "admin/views/images/questoes/estrela_vazia.jpg");
    }

    for(var i=1; i<=dados[1]; i++){
        $("#" + dados[0] + "_" + i).attr("src", "admin/views/images/questoes/estrela_cheia.jpg");
    }

    $("#nota_" + dados[0]).val(dados[1]);

    return false;
}