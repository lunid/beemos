/*
 * Classe de abstração para funções JavaScript da Home
 **/
Top10 = function(){};

Top10.prototype = {
    init: function(){
        
    },
    selecionaFiltro: function(filtro){
        $("input[name=tipo_filtro]").each(function(){
            $("#" + this.value).css("background-color", "#DADADA");
            $("#" + this.value).attr("disabled", "disabled");
            $("#" + this.value).val(0);
        });

        $("#" + filtro).css("background-color", "#FFF");
        $("#" + filtro).removeAttr("disabled");
    },
    atualizaUsuarioQuestao: function(id_questao, id_usuario){
        if(confirm("Tem certeza que deseja alterar o usuário.\nIsso pode limpar avaliações anteriores")){
            $.post(
                "top10_avaliar_questoes.php",
                {
                    id_questao: id_questao,
                    id_usuario: id_usuario,
                    hdd_acao: 'usuario_questao'
                },
                function(ret){
                    alert(ret.msg);
                },
                'json'
            );
        }else{
            return false;
        }
    }
};

$(document).ready(function() {
    top10 = new Top10();
    top10.init();
});