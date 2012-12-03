function escrever(){
    $("#modal_escrever").dialog({
        title: "Escrever Mensagem",
        modal: true,
        width: 800
    });
}

function listarPara(){
    //Efetua chamada Ajax para montar grid de alunos
    //Inicializando o Grid de Escolas (Aba Escolas & Turmas)
    $("#grid_para").jqGrid({
        url: 'caixapostal/listarAlunosPara',
        datatype: "json",
        colNames:['', 'COD', 'Nome', 'Escola', 'Turma'],
        colModel:[
                //site.formataGrid é a função responsável por tratar os erros do jSon, assim como o estilo da primeira coluna
                {name:'BOX', index:'BOX', width:10, align:'center', search: false, cellattr: site.formataGrid, sortable: false },
                {name:'ID_CLIENTE', index:'ID_CLIENTE', width:25, align:'center', search: true},
                {name:'NOME_PRINCIPAL', index:'NOME_PRINCIPAL', search: true},
                {name:'ESCOLA', index:'ESCOLA', width:70, align:'center', search: true},
                {name:'CLASSE', index:'CLASSE', width:30, align:'center', search: true}
        ],
        rowNum:10,
        rowList:[10,30,60],
        pager: '#pg_para',
        sortname: 'NOME_PRINCIPAL',
        viewrecords: true,
        sortorder: "ASC",
        caption:"Alunos",
        width: 750,
        height: 'auto',
        scrollOffset: 0
    });

    $("#grid_para").filterToolbar();
        
    $("#modal_para").dialog({
        title: "Selecione os alunos",
        modal: true,
        width: 800,
        position: [50, 50]
    });
}

/**
 * Marca ou Desmarca todos alunos do Grid Para
 */
function selecionarTodosAlunos(obj){
    $(".check_aluno").each(function(){
       this.checked = obj.checked; 
    });
}

function finalizaPara(opt){
    if(opt == true){
        //Cancatena e-mails em um string separados por pont e vírgula
        var emails = "";
        $(".check_aluno").each(function(){
            //Caso o alunos esteja seleciona seu e-mail é concatenado
            if(this.checked){
                if(emails != ""){
                    emails += "; ";
                }
                emails += this.value; 
            }
        });
        
        //Se nenhum e-mail foi encontrado, é devolvido um alerta
        if(emails == ""){
            alert("Selecione no mínimo um aluno!");
        }else{
            //Senão, os e-mail são adicionados ao PARA e a caixa de dialogo finalizada
            $("#escrever_para").val(emails);
            
            $("#modal_para").dialog("close");
        }
    }else if(opt == false){
        //Se clicar em cancelar, a caixa de dialogo é finalizada
        $("#modal_para").dialog("close");
    }
}