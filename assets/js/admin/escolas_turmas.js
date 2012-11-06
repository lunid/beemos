$(document).ready(function(){
    //Habilitando componente de Abas
    $( "#abas" ).tabs({ active:0 });
    
    //Inicializando o Grid
    $("#grid_escola").jqGrid({
        url: 'GridEscolas',
        datatype: "json",
        colNames:['COD', 'Escola', 'Status', ''],
        colModel:[
                {name:'ID_ESCOLA', index:'ID_ESCOLA', width:25, align:'center', search: true},
                {name:'NOME', index:'NOME', search: true},
                {name:'STATUS', index:'STATUS', width:50, align:'center', search: true, stype: 'select', searchoptions:{ value: "-1:Todas;1:Ativa;0:Inativa" }},
                {name:'Turmas', index:'Turmas', width:30, align:'center', search: false, sortable: false}
        ],
        rowNum:15,
        rowList:[15,25,35],
        pager: '#pg_escola',
        sortname: 'ID_ESCOLA',
        viewrecords: true,
        sortorder: "desc",
        caption:"Escolas",
        width: 750,
        height: 300,
        scrollOffset: 0
    });
                
    $("#grid_escola").filterToolbar();
                
    $("#grid_escola")
        .navGrid('#pg_escola',{edit:false,add:false,del:false,search:false})
        .navButtonAdd('#pg_escola',{
            caption: "Nova Escola", 
            buttonicon: "ui-icon-plus", 
            onClickButton: function(){ 
                $.fancybox.open([
                    {
                        href: '#modal_aguarde',
                        helpers: {
                            overlay : {
                                closeClick : false
                            }
                        },
                        height: 40
                    }
                ]);
            }, 
            position:"last"
    });
});