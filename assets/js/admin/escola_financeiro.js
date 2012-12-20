$(document).ready(function(){
    //Carrega Grid de Acessos
    $("#grid_financeiro").jqGrid({
        url: 'gridFinanceiro',
        datatype: "json",
        hidegrid: false,
        colNames:['Pedido', 'Nº Doc', 'Data / Compra', 'Parcelas', 'Valor Parcela',  'Vencimento', 'Pago em', 'Status'],
        colModel:[
            //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
            {name:'NUM_PEDIDO_PAI', index:'NUM_PEDIDO_PAI', width:15, align:'center', search: true, cellattr: site.formataGrid, sortable: true },
            {name:'NUM_PEDIDO', index:'NUM_PEDIDO', width:15, search: true, align:'center'},
            {
                name:'DATA_REGISTRO', index:'DATA_REGISTRO', width:30, align:'center', search: true,
                searchoptions:{
                    dataInit:function(elem){
                        $(elem).datepicker({
                            onSelect: function() {
                                $("#grid_financeiro")[0].triggerToolbar();
                            }
                        });
                    }
                }
            },
            {name:'PARCELAS', index:'PARCELAS', width:10, search: true, align:'center'},
            {name:'VALOR_PARCELA', index:'VALOR_PARCELA', width:20, search: true, align:'center'},
            {
                name:'DATA_VENC_BOLETO', index:'DATA_VENC_BOLETO', width:30, align:'center', search: true,
                searchoptions:{
                    dataInit:function(elem){
                        $(elem).datepicker({
                            onSelect: function() {
                                $("#grid_financeiro")[0].triggerToolbar();
                            }
                        });
                    }
                }
            },
            {
                name:'DATA_PGTO', index:'DATA_PGTO', width:30, align:'center', search: true,
                searchoptions:{
                    dataInit:function(elem){
                        $(elem).datepicker({
                            onSelect: function() {
                                $("#grid_financeiro")[0].triggerToolbar();
                            }
                        });
                    }
                }
            },
            {name:'STATUS', index:'STATUS', width:15, search: true, stype:'select', searchoptions:{ value: "0:Todos;1:A Vencer;2:Pagos;3:Pendentes" }, align:'center'}
        ],
        rowNum:10,
        rowList:[10,20,30],
        pager: '#pg_financeiro',
        sortname: 'DATA_REGISTRO',
        viewrecords: true,
        sortorder: "DESC",
        caption:"Pedidos - Financeiro",
        width: 900,
        height: 'auto',
        scrollOffset: 0
    });
                
    $("#grid_financeiro").filterToolbar();
});