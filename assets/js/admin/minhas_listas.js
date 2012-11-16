$(document).ready(function(){
    //Habilitando componente de Abas
    tabs        = $( "#abas" ).tabs({ active:0 });
    tabTemplate = "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close'>Remove Tab</span></li>",
    tabCounter  = 2;
    
    //Carrega Grid de Listas - Aba principal
    $("#grid_listas").jqGrid({
        url: 'GridListas',
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
    });
});

function abreLista(idLista, nomeAba){
    var id = "editar_" + idLista;
    var li = $( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, nomeAba ) );

    tabs.find( ".ui-tabs-nav" ).append( li );
    tabs.append( "<div id='" + id + "'>" + $("#editar").html() + "</div>" );
    tabs.tabs( "refresh" );
    $( "#ui-id-" + tabCounter ).trigger('click');
    tabCounter++;
}

