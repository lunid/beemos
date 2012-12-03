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
        'listas/carregarHtmlAbaLista',
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
                //Cria nova Aba
                var id      = "editar_" + idLista;
                var li      = $( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, nomeAba ) );

                tabs.find( ".ui-tabs-nav" ).append( li );
                tabs.append( "<div id='" + id + "'>" + ret.html + "</div>" );
                tabs.tabs( "refresh" );
                $( "#ui-id-" + tabCounter ).trigger('click');
                tabCounter++;
                
                // ************************************************************************************************************
                // ************************************** Ações para área de Configurações ************************************
                // ************************************************************************************************************
               
                //Aplica datepicker                
                //Objeto para capturar informações de data
                var date = new Date();
                var ano = date.getFullYear();
                var mes = date.getMonth();
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
                
                // ************************************************************************************************************
                // ************************************** Ações para área de Resultados ***************************************
                // ************************************************************************************************************
                
                //Carregas Filtros
                filtrosResultados(idLista);
                
                //Carrega gráficos da lista
                geraGrafico(idLista);
                
                // ************************************************************************************************************
                // ************************************** Ações para área de Alunos ***************************************
                // ************************************************************************************************************
                // 
                //Carrega Grid de Alunos que estão atrelados na lista - Aba Alunos
                $("#grid_alunos_status_" + idLista).jqGrid({
                    url: 'listas/CarregarAlunosLista?idLista=' + idLista,
                    datatype: "json",
                    colNames:['Código', 'Aluno', 'Escola', 'Turma', 'Concluída'],
                    colModel:[
                            //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
                            {name:'ID_CLIENTE', index:'ID_CLIENTE', width:15, align:'center', search: true, cellattr: site.formataGrid },
                            {name:'ALUNO', index:'ALUNO', width:40, search: true},
                            {name:'ESCOLA', index:'ESCOLA', width:20, search: true},
                            {name:'TURMA', index:'TURMA', width:20, search: true},
                            {name:'CONCLUIDA', index:'CONCLUIDA', width:20, search: true, stype: 'select', searchoptions:{ value: "0:Todas;1:Concluída;2:Não Concluída" }}
                    ],
                    rowNum:30,
                    rowList:[30,60,100],
                    pager: '#pg_alunos_status_' + idLista,
                    sortname: 'ALUNO',
                    viewrecords: true,
                    sortorder: "ASC",
                    caption:"Alunos",
                    width: 750,
                    height: 'auto',
                    scrollOffset: 0
                });

                $("#grid_alunos_status_" + idLista).filterToolbar();
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
 * Verifica (dentro de um select ou multi-select se a opção Todos(as)
 * foi selecionada. Se sim, desmarca as outras oções.
 */
function verificaOptTodas(id){
    var ver = false //Verificação de TODOS;
    
    //Verifica se uma das opções é TODOS(AS)
    $(id + " option").each(function(){ 
        if(this.selected && this.value == 0){
            ver = true;
        } 
    });
    
    //Se a opção TODOS(AS) for selecionada desmaraca as outras
    if(ver){
        $(id + " option").each(function(){ 
            if(this.value == 0){
                this.selected = true;
            }else{
                this.selected = false;
            } 
        });
    }
    
    return $(id).val();
}

/**
 * Monta filtros para gráfico de acordo com o que o usuário
 * vai selecionando
 */
function filtrosResultados(idLista){
    var idEscola    = $("#escolas_" + idLista).val();
    var ensino      = verificaOptTodas("#ensino_" + idLista);
    var periodo     = verificaOptTodas("#periodo_" + idLista);
    var ano         = verificaOptTodas("#ano_" + idLista);
    
    $.post(
        'listas/CarregarFiltrosResultados',
        {
            idLista: idLista,
            idEscola: idEscola,
            ensino: ensino,
            periodo: periodo,
            ano: ano
        },
        function(ret){
            var escolasOpts     = "<option value='0' selected='selected'>Todas</option>"; //Opções do select de Escolas
            var ensinoOpts      = "<option value='0' selected='selected'>Todos</option>"; //Opções do select de Ensinos
            var periodoOpts     = "<option value='0' selected='selected'>Todas</option>"; //Opções do select de Periodos
            var anoOpts         = "<option value='0' selected='selected'>Todos</option>"; //Opções do select de Anos
            var turmasOpts      = "<option value='0' selected='selected'>Todas</option>"; //Opções do select de Turmas

            //Verifica retorno de Escolas e Turmas relacionas a Lista
            if(ret.escolasTurmas.status){
                //Popula html do select de escolas
                $(ret.escolasTurmas.arrEscolas).each(function(){
                    for(var idEscola in this){
                        escolasOpts += "<option value='" + this[idEscola].ID_ESCOLA + "'>" + this[idEscola].ESCOLA + "</option>";
                    }
                });

                //Popula html do select de ensinos
                $(ret.escolasTurmas.arrEnsino).each(function(){
                    for(var idEnsino in this){
                        ensinoOpts += "<option value='" + this[idEnsino].ENSINO + "'>" + this[idEnsino].DESC + "</option>";
                    }
                });

                //Popula html do select de períodos
                $(ret.escolasTurmas.arrPeriodo).each(function(){
                    for(var idPeriodo in this){
                        periodoOpts += "<option value='" + this[idPeriodo].PERIODO + "'>" + this[idPeriodo].DESC + "</option>";
                    }
                });

                //Popula html do select de períodos
                $(ret.escolasTurmas.arrAno).each(function(){
                    for(var idAno in this){
                        anoOpts += "<option value='" + this[idAno] + "'>" + this[idAno] + "</option>";
                    }
                });

                //Popula html do select de turmas
                $(ret.escolasTurmas.arrTurmas).each(function(){
                    for(var idTurma in this){
                        turmasOpts += "<option value='" + this[idTurma].ID_TURMA + "'>" + this[idTurma].CLASSE + "</option>";
                    }
                });
            }

            //Popula selects com as informações encontradas
            if(idEscola <= 0 || idEscola == null){ $("#escolas_" + idLista).html(escolasOpts); }
            if(ensino <= 0 || ensino == null){ $("#ensino_" + idLista).html(ensinoOpts); }
            if(periodo <= 0 || periodo == null){ $("#periodo_" + idLista).html(periodoOpts); }
            if(ano <= 0 || ano == null){ $("#ano_" + idLista).html(anoOpts); }
            $("#turmas_" + idLista).html(turmasOpts);
        },
        'json'
    );
}

/**
 * Função que geras gráficos de uma determinada lista
 */
function geraGrafico(idLista){
    //Pega dados de filtros
    var idEscola    = $("#escolas_" + idLista).val();
    var ensino      = $("#ensino_" + idLista).val();
    var periodo     = $("#periodo_" + idLista).val();
    var ano         = $("#ano_" + idLista).val();
    var turma       = $("#turmas_" + idLista).val();
    
    //Modal aguarde
    site.aguarde();
    
    $.post(
        'listas/GerarGraficosResultados',
        {
            idLista: idLista,
            idEscola: idEscola,
            ensino: ensino,
            periodo: periodo,
            ano: ano,
            turma: turma
        },
        function(ret){
            //Fecha modal
            site.fechaAguarde();
            
            //Verifica se houve retorno
            if(ret.status){
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
                            height: 200,
                            borderWidth: 1,
                            borderColor: '#909090'
                        },
                        exporting: {
                            enabled: false
                        },
                        credits: {
                            enabled: false
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
                                },
                                showInLegend: true,
                                size: '95%'
                            }
                        },
                        legend:{
                            borderWidth: 0,
                            layout: 'vertical',
                            itemStyle: {
                                fontSize: '10px'
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
                }else{
                    $("#gr_respostas_" + idLista).html("<p>Respostas<br /><span>0</span></p>");
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
                            height: 200,
                            borderWidth: 1,
                            borderColor: '#909090'
                        },
                        exporting: {
                            enabled: false
                        },
                        credits: {
                            enabled: false
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
                                },
                                showInLegend: true,
                                size: '95%'
                            }
                        },
                        legend:{
                            borderWidth: 0,
                            layout: 'vertical',
                            itemStyle: {
                                fontSize: '10px'
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
                }else{
                    $("#gr_alunos_" + idLista).html("<p>Alunos<br /><span>0</span></p>");
                } //Gráficio de alunos
                
                //Informações de aproveitamento
                if(ret.APROVEITAMENTO.status){
                    $("#num_proveitamento_" + idLista).html(ret.APROVEITAMENTO.aproveitamento);      
                    $("#tmp_aproveitamento_" + idLista).val(ret.APROVEITAMENTO.aproveitamento);
                }else{
                    $("#aproveitamento_" + idLista).html("<p>Aproveitamento<br /><span id='num_proveitamento_"+idLista+"'>0</span>%</p>");
                    $("#tmp_aproveitamento_" + idLista).val(0);
                }
                
                //Verifica se foram encontradas informações de respostas
                if(ret.GR_QUESTOES.status){
                    //Monta array de colunas
                    var colunas     = Array(ret.GR_QUESTOES.questoes.length);
                    var corretas    = Array(ret.GR_QUESTOES.questoes.length);
                    var i           = 0;
                    
                    $(ret.GR_QUESTOES.questoes).each(function(){
                        colunas[i]  = this.ID_BCO_QUESTAO;
                        corretas[i] = this.aproveitamento;
                        
                        i++;
                    });
                    
                    //Exibe gráfico
                    $("#gr_questoes_" + idLista).show();
                    
                    //Gráfico
                    var gr_questoes = new Highcharts.Chart({
                        chart: {
                            renderTo: 'gr_questoes_' + idLista,
                            type: 'bar',
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            width: 750,
                            borderWidth: 1,
                            borderColor: '#909090'
                        },
                        exporting: {
                            enabled: false
                        },
                        title: {
                            text: 'Aproveitamento por Questão (%)'
                        },
                        xAxis: {
                            categories: colunas
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Aproveitamento (%)'
                            }
                        },
                        tooltip: {
                            formatter: function() {
                                return this.y + '% de acertos';
                            }
                        },
                        plotOptions: {
                            bar: {
                                stacking: 'normal'
                            }
                        },
                        legend:{
                            enabled: false
                        },
                        series: [
                            {
                                name: 'Corretas',
                                data: corretas
                            }
                        ],
                        credits: {
                            enabled: false
                        }
                    });   
                }else{
                  $("#gr_questoes_" + idLista).hide();  
                } //Gráfico de Questões
                
                //Redimensiona área do gráfico de universo x aluno e esconde
                $("#gr_aluno_" + idLista).hide();
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
 * Abre um modal com o Grid de Alunos que responderam a lista.
 * Ao selecionar um aluno, será gerado o gráfico de aproveitamento dele x universo (já pesquisado)
 */
function modalAluno(idLista){
    //Aguarde
    site.aguarde();
    
    //Carrega Grid de Alunos
    $("#grid_alunos_" + idLista).jqGrid({
        url: 'listas/CarregarAlunosLista?idLista=' + idLista + '&responderam=1',
        datatype: "json",
        colNames:['Código', 'Aluno', 'Escola', 'Turma'],
        colModel:[
                //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
                {name:'ID_CLIENTE', index:'ID_CLIENTE', width:15, align:'center', search: true, cellattr: site.formataGrid },
                {name:'ALUNO', index:'ALUNO', width:40, search: true},
                {name:'ESCOLA', index:'ESCOLA', width:20, search: true},
                {name:'TURMA', index:'TURMA', width:20, search: true}
        ],
        onSelectRow: function(id){ 
            //Modal Aguarde
            site.aguarde();
            
            //tmp_aproveitamento_
            $.post(
                'listas/GraficoAluno',
                {
                    idLista: idLista,
                    idCliente: id
                },
                function(ret){
                    //Fecha aguarde
                    site.fechaAguarde();
                    
                    if(ret.status){
                        var universo    = parseFloat($("#tmp_aproveitamento_" + idLista).val());
                        var aluno       = ret.aproveitamento;
                        
                        //Gráfico
                        var gr_aluno = new Highcharts.Chart({
                            chart: {
                                renderTo: 'gr_aluno_' + idLista,
                                type: 'column',
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                width: 750,
                                height: 300,
                                borderWidth: 1,
                                borderColor: '#909090'
                            },
                            exporting: {
                                enabled: false
                            },
                            title: {
                                text: 'Aproveitamento Universo x Aluno (%)'
                            },
                            subtitle: {
                                text: 'Aluno(a) - ' + ret.aluno,
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold'
                                }
                            },
                            xAxis: {
                                categories: ['Universo', 'Aluno(a)']
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: 'Aproveitamento (%)'
                                }
                            },
                            tooltip: {
                                formatter: function() {
                                    return this.y + '%';
                                }
                            },
                            plotOptions: {
                                column: {
                                    stacking: 'normal'
                                }
                            },
                            legend: {
                                enabled: false
                            },
                            series: [
                                {
                                    name: 'Aproveitamento',
                                    data: [
                                        {
                                            y: universo,
                                            color: universo > aluno ? '#4572A7' : '#AA4643'                                            
                                        },
                                        {
                                            y: aluno,
                                            color: aluno > universo ? '#4572A7' : '#AA4643'                                            
                                        }
                                    ]
                                }
                            ],
                            credits: {
                                enabled: false
                            }
                        });
                        
                        //Redimensiona área do gráfico e exibe
                        $("#gr_aluno_" + idLista).show();
                    }else{
                        alert(ret.msg);
                    }
                },
                'json'
            );
            
            $( "#modal_alunos_" + idLista ).dialog("close");
        },
        rowNum:10,
        rowList:[10,20,30],
        pager: '#pg_alunos_' + idLista,
        sortname: 'ALUNO',
        viewrecords: true,
        sortorder: "ASC",
        caption:"Alunos",
        width: 750,
        height: 'auto',
        scrollOffset: 0
    });
                
    $("#grid_alunos_" + idLista).filterToolbar();
    
    //Fecha aguarde
    site.fechaAguarde();
    
    //Abre modal com grid
    $("#modal_alunos_" + idLista).dialog({
        title: "Selecione um Aluno",
        open: function(event, ui) { $(".ui-dialog-titlebar").show(); },
        modal: true,
        width: 780,
        heigth: 550
    });
}

/**
 * Abre a tela de impressão de gráficos
 */
function imprimirGraficos(idLista){
    window.open("listas/ImprimirGraficos?idLista=" + idLista, "Imprimir", "width=800,height=600,status=no,scrollbars=yes");
}

/**
 * Altera opção de Anticola da Lista
 */
function alteraAnticola(status, idLista){
    //Inicial modal de aguarde
    site.aguarde();
    
    $.post(
        'listas/alterarAnticola',
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
        'listas/alterarPeriodo',
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
        'listas/alterarResultadoAluno',
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
        'listas/alterarGabaritoAluno',
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
            'listas/alterarTempoVida',
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

/**
 * Controla navegação do usuário no menu interno da Aba
 */
function menuLista(menu){
    var arr = menu.split("_");
    
    $("." + arr[2]).css("display", "none"); //Oculta todas DIVs
    $("#" + menu).css("display", ""); //Exibe apenas a escolhida
}