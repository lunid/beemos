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
        
        //Adiciona função ao botão
        $("#btGeraGrafico").bind('click', function(){
            top10.geraGrafico();
        });
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
    geraGrafico: function(){
        $("#aguarde").show();
        
        $.post(
            'top10/geraGrafico',
            {
                data_inicio: $("#data_inicio").val(),
                data_final: $("#data_final").val(),
                hdd_acao: 'filtar_grafico'
            },
            function(ret){
                $("#aguarde").hide();
                
                if(ret.status){
                    if(ret.html != null){
                        $("#graphsContent").html(ret.html);
                        iniciaGraficoTop10();
                    }else{
                        $("#graphsContent").html(ret.msg);
                    }
                }else{
                    alert(ret.msg);
                }
            },
            'json'
        );
    }
};

$(document).ready(function() {
    top10 = new Top10();
    top10.init();
});