
$(document).ready(function() {
    var fldModulo = new LiveValidation('MODULO',{validMessage: 'Obrigado!'},{onlyOnSubmit: true } );
    fldModulo.add( Validate.Presence, { failureMessage: "Por favor, informe o nome da pasta para o módulo atual!" } );   
    fldModulo.add( Validate.Length,{minimum: 4, maximum: 10,tooShortMessage:"O nome da pasta deve ter no mínimo 4 e no máximo 20 caracteres."});
});

function addNovoController(indice){
    var blqName = 'blqController_'; 
    var i       = $('#'+blqName+indice+' fieldset').size() + 1;
   
    var id      = blqName+indice;
    var idItem  = id+'_'+i;
    var scntDiv = $('#'+id);       
    var idField = 'controller_'+i;    
    
    
    html    = "<fieldset id='"+idItem+"'>";
    html    += "    <legend>Novo Controller</legend>";
    html    += "    <a href='#' onclick=\"delItem('"+idItem+"')\" class='linkDel'>Excluir</a><br>";    
    html    += "        <label for='blqController'>Nome do Controller</label>";
    html    += "        <input type='text' id='"+idField+"' size='20' name='CONTROLLER' value='' placeholder='Nome do Controller' /><span class='prefixoSufixo'>Controller.php</span>";
    html    += "        <div id='actions'>";
    html    += "            Actions do controller atual: <a href='#' onclick='addNovaAction("+i+")' id='addAction'>Nova action...</a>";
    html    += "            <div id='blqAction_"+i+"'><p>";
    html    += "                <label for='blqAction'>Nome da action:</label> <input type='text' id='action_1' size='20' name='action_1' value='' />";
    html    += "            </p></div>";
    html    += "</fieldset>";

    $(html).appendTo(scntDiv);
    i++;    
}

function addNovaAction(indice){
    var blqName = 'blqAction_'; 
    var i       = $('#'+blqName+indice+' p').size() + 1;
    var id      = blqName+indice;
    var idItem  = id+'_'+i;
    var scntDiv = $('#'+id);       
    var idField = 'action_'+i;
    
    html        = "<p id='"+idItem+"'>";
    html        += "<label for='blqAction'>Nome da action:</label> ";
    html        += "<span class='prefixoSufixo'>action</span><input type='text' id='"+idField+"' size='30' width='200px' name='ACTION' value='' /><span class='prefixoSufixo'>( ) {...}</span>";
    html        += "<a href='#' onclick='delItem(\""+idItem+"\")' class='linkDel'>Excluir</a>";
    html        += "</p>";
    
    $(html).appendTo(scntDiv);
    i++;

}

function delItem(id){    
    $('#'+id).remove();
}