$(document).ready(function(){
    //Habilitando componente de Abas
    tabs        = $( "#abas" ).tabs({ active:0 });
    tabTemplate = "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close'>Remove Tab</span></li>",
    tabCounter  = 3;
    
    //Carrega Grid de Listas - Aba principal
    $("#grid_listas").jqGrid({
        url: 'listas/gridlistas',
        datatype: "json",
        colNames:['Código', 'Nome', 'Data Criação', 'Impressão', 'Resultado', 'Status', ''],
        colModel:[
                //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
                {name:'COD_LISTA', index:'COD_LISTA', width:15, align:'center', search: true, cellattr: site.formataGrid },
                {name:'DESCR_ARQ', index:'DESCR_ARQ', width:40, search: true},
                {name:'DATA_REGISTRO', index:'DATA_REGISTRO', width:12, align:'center', search: true},
                {name:'VER_IMPRESSA', index:'VER_IMPRESSA', width:10, align:'center', search: false},
                {name:'RESULTADO', index:'RESULTADO', width:10, align:'center', search: false, sortable: false},
                {name:'STATUS', index:'STATUS', width:10, align:'center', search: true, sortable: false},
                {name:'EDITAR', index:'EDITAR', width:10, align:'center', search: false, sortable: false}
        ],
        rowNum:10,
        rowList:[10,20,30],
        pager: '#pg_listas',
        sortname: 'DATA_REGISTRO',
        viewrecords: true,
        sortorder: "DESC",
        caption:"Listas de Exercícios",
        width: 750,
        height: 'auto',
        scrollOffset: 0
    });
                
    $("#grid_listas").filterToolbar();
    
    //Função para fechar abas ao clicar no fechar
    $( "#abas span.ui-icon-close" ).live( "click", function() {
        var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
        $( "#" + panelId ).remove();
        tabs.tabs( "refresh" );
        $("#controleAbas").val($("#controleAbas").val().replace(panelId, ''));
    });
});

/**
 * Abre uma nova Aba com a Lista seleciona no grid e seus detalhes
 */
function abreLista(idLista, nomeAba){
    //Verifica se já não existe a Aba
    var controleAbas    = $("#controleAbas").val();
    var ver             = controleAbas.search("editar_" + idLista);
    
    if(ver >= 0){
        $("a[href=#editar_" + idLista + "]").trigger('click');
        return false;
    }
    
    
    //Inicial modal de aguarde
    site.aguarde();
    
    $.post(
        'listas/carregahtmlabalista',
        {
            idLista: idLista
        },
        function(ret){
            //Fecha aguarde
            site.fechaAguarde();
            
            if(!ret.status){
                //Exibe erro, caso exista
                site.modal(ret.msg, "Abrir Lista", "erro");
            }else{
                var id      = "editar_" + idLista;
                var li      = $( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, nomeAba ) );

                tabs.find( ".ui-tabs-nav" ).append( li );
                tabs.append( "<div id='" + id + "'>" + ret.html + "</div>" );
                tabs.tabs( "refresh" );
                $( "#ui-id-" + tabCounter ).trigger('click');
                tabCounter++;
                
                //Aplica datepicker
                
                //Objeto para capturar informações de data
                var date = new Date();
                var ano = date.getFullYear();
                var mes = date.getMonth()+1;
                var dia = date.getDate();
                
                $(".periodo_ini").datepicker({
                    minDate: new Date(ano, mes, dia)
                });
                
                $(".periodo_fim").datepicker({
                    minDate: new Date(ano, mes, dia)
                });
                
                //Aplica máscara de tempo HH:MM
                $(".tempo_vida").mask('99:99');
                
                //Armazena controle de Abas
                if(controleAbas != ''){
                    controleAbas += ',';
                }
                controleAbas += id;
                
                $("#controleAbas").val(controleAbas);
                
                //Verifica se foram encontradas informações de respostas
                if(ret.GR_RESPOSTAS.status){
                    //Gráficos
                    var grRespostas = new Highcharts.Chart({
                        chart: {
                            renderTo: 'gr_respostas_' + idLista,
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            width: 200,
                            height: 200
                        },
                        title: {
                            text: 'Respostas'
                        },
                        tooltip: {
                                pointFormat: '<b>{point.y}</b>',
                                percentageDecimals: 2
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: false
                                }
                            }
                        },
                        series: [{
                            type: 'pie',
                            data: [
                                ['Corretas',   ret.GR_RESPOSTAS.correta],
                                ['Erradas',    ret.GR_RESPOSTAS.errada]
                            ]
                        }]
                    });                    
                } //Gráficio de Respostas
                
                //Verifica se foram encontradas informações de alunos
                if(ret.GR_ALUNOS.status){
                    //Gráficos
                    var grAlunos = new Highcharts.Chart({
                        chart: {
                            renderTo: 'gr_alunos_' + idLista,
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            width: 200,
                            height: 200
                        },
                        title: {
                            text: 'Alunos'
                        },
                        tooltip: {
                                pointFormat: '<b>{point.y}</b>',
                                percentageDecimals: 2
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: false
                                }
                            }
                        },
                        series: [{
                            type: 'pie',
                            data: [
                                ['Responderam',     ret.GR_ALUNOS.respondeu],
                                ['Não Responderam', ret.GR_ALUNOS.naoRespondeu]
                            ]
                        }]
                    });                    
                } //Gráficio de alunos
            }
        },
        'json'
    ).error(
        //Exibe ALERT em caso de erro fatal
        function(){
            site.fechaAguarde();
            alert("Falha no servidor! Tente mais tarde.");
        }
    );
}

/**
 * Altera opção de Anticola da Lista
 */
function alteraAnticola(status, idLista){
    //Inicial modal de aguarde
    site.aguarde();
    
    $.post(
        'listas/alteraanticola',
        {
            idLista: idLista,
            status: status
        },
        function(ret){
            //Fecha aguarde
            site.fechaAguarde();
            
            if(!ret.status){
                //Exibe erro, caso exista
                site.modal(ret.msg, "Anticola", "erro");
            }
        },
        'json'
    ).error(
        //Exibe ALERT em caso de erro fatal
        function(){
            site.fechaAguarde();
            alert("Falha no servidor! Tente mais tarde.");
        }
    );
}

/**
 * Altera da data de inicio ou fim da validade da prova
 */
function alteraPeriodo(data, tipo, idLista){
    //Inicial modal de aguarde
    site.aguarde();
    
    $.post(
        'listas/alteraperiodo',
        {
            data: data,
            tipo: tipo,
            idLista: idLista
        },
        function(ret){
            //Fecha aguarde
            site.fechaAguarde();
            
            if(!ret.status){
                //Exibe erro, caso exista
                site.modal(ret.msg, "Período", "erro");
            }
        },
        'json'
    ).error(
        //Exibe ALERT em caso de erro fatal
        function(){
            site.fechaAguarde();
            alert("Falha no servidor! Tente mais tarde.");
        }
    );
}

/**
 * Alterar status de permissão do Resultado ao Aluno (0 ou 1)
 */
function alteraResultadoAluno(status, idLista){
    //Inicial modal de aguarde
    site.aguarde();
    
    $.post(
        'listas/alteraresultadoaluno',
        {
            idLista: idLista,
            status: status
        },
        function(ret){
            //Fecha aguarde
            site.fechaAguarde();
            
            if(!ret.status){
                //Exibe erro, caso exista
                site.modal(ret.msg, "Resultado do Aluno", "erro");
            }
        },
        'json'
    ).error(
        //Exibe ALERT em caso de erro fatal
        function(){
            site.fechaAguarde();
            alert("Falha no servidor! Tente mais tarde.");
        }
    );
}

/**
 * Alterar status de permissão do Gabarito ao Aluno (0 ou 1)
 */
function alteraGabaritoAluno(status, idLista){
    //Inicial modal de aguarde
    site.aguarde();
    
    $.post(
        'listas/alteragabaritoaluno',
        {
            idLista: idLista,
            status: status
        },
        function(ret){
            //Fecha aguarde
            site.fechaAguarde();
            
            if(!ret.status){
                //Exibe erro, caso exista
                site.modal(ret.msg, "Gabarito do Aluno", "erro");
            }
        },
        'json'
    ).error(
        //Exibe ALERT em caso de erro fatal
        function(){
            site.fechaAguarde();
            alert("Falha no servidor! Tente mais tarde.");
        }
    );
}

/**
 * Altera o empo limite que o aluno possui para finalizar a lista
 */
function alteraTempoVida(tempo, idLista){
    //Expressão regular para validar o campo
    var regTempo = /^[0-9][0-9]:[0-9][0-9]/;
    
    //Verifica se o valor está no padrão 00:00 e salva informação
    if(regTempo.test(tempo)){
        $.post(
            'listas/alteraTempoVida',
            {
                idLista: idLista,
                tempo: tempo
            },
            function(ret){
                if(!ret.status){
                    //Exibe erro, caso exista
                    site.modal(ret.msg, "Tempo de Vida", "erro");
                }
            },
            'json'
        ).error(
            //Exibe ALERT em caso de erro fatal
            function(){
                site.fechaAguarde();
                alert("Falha no servidor! Tente mais tarde.");
            }
        );
    }
}