/*
 * Classe de abstração para funções JavaScript da Home
 **/
Top10 = function(){};

Top10.prototype = {
    init: function(){
        $( "#data_inicio" ).datepicker({
            numberOfMonths: 2,
            onSelect: function( selectedDate ) {
                var mes         = null;
                var setDataVal  = null;
                var data_f      = selectedDate.split("/");

                var minDate = new Date(data_f[1] + "/" + data_f[0] + "/" + data_f[2]);
                var maxDate = new Date(data_f[1] + "/" + data_f[0] + "/" + data_f[2]);
                var today   = new Date();

                maxDate.setDate(maxDate.getDate()+30);

                if(maxDate > today){
                    mes         = (today.getMonth()+1) < 10 ? "0" + (today.getMonth()+1) : (today.getMonth()+1);
                    setDataVal  = today.getUTCDate() + "/" + mes + "/" + today.getFullYear();

                    $( "#data_final" ).datepicker( "setDate", today );
                    $( "#data_final" ).val(setDataVal);
                    $( "#data_final" ).datepicker( "option", "maxDate", today );
                }else{
                    mes         = (maxDate.getMonth()+1) < 10 ? "0" + (maxDate.getMonth()+1) : (maxDate.getMonth()+1);
                    setDataVal  = maxDate.getUTCDate() + "/" + mes + "/" + maxDate.getFullYear();

                    $( "#data_final" ).datepicker( "setDate", maxDate );
                    $( "#data_final" ).val(setDataVal);
                    $( "#data_final" ).datepicker( "option", "maxDate", maxDate );
                }

                $( "#data_final" ).datepicker( "option", "minDate", minDate );
            }
        });
        
        var today = new Date();
        $( "#data_inicio" ).datepicker( "option", "maxDate", today );
        
        $( "#data_final" ).datepicker({
            numberOfMonths: 2
        });
        
        var data_f  = $( "#data_inicio" ).val().split("/");
        var minDate = new Date(data_f[1] + "/" + data_f[0] + "/" + data_f[2]);
        $("#data_final" ).datepicker("option", "minDate", minDate);
        
        data_f      = $( "#data_final" ).val().split("/");
        var maxDate = new Date(data_f[1] + "/" + data_f[0] + "/" + data_f[2]);
        $( "#data_final" ).datepicker( "option", "maxDate", maxDate );
        
        //Função inserida no ocmponente de gráfico
        iniciaGraficoTop10();
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