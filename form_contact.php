<?
    require_once 'class/contato.php';
    
    if($_POST){
        $contato = new Contato();
        
        $contato->setNome($_POST['nome']);
        $contato->setEmail($_POST['email']);
        $contato->setAssunto($_POST['assunto']); 
        $contato->setMensagem($_POST['msg']);
        
        if($contato->save()){
            echo "E-mail enviado com sucesso!<br /><br />";
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Formulário de Contato</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style type="text/css">
            .form .input_text{
                width:200px;
            }
            
            .form .textarea{
                width:200px;
                height:200px;
            }
        </style>
        
        <script type="text/javascript" src="js/libs/jquery_171.js"></script>
        <script type="text/javascript" src="js/libs/tooltip/jquery_qtip_100_min.js"></script> 
        <script type="text/javascript" src="js/libs/util/dictionary.js"></script>
        <script type="text/javascript" src="js/libs/util/form.js"></script>
    </head>
    <body>
        <form id="form_contact" name="form_contact" action="form_contact.php" method="post" class="form" onsubmit="return form.validate(this);">
            <table align="left" cellpading="3" cellspacing="1" border="1">
                <tr>
                    <td>
                        Nome
                    </td>
                    <td>
                        <input type="text" id="nome" name="nome" class="input_text required" field_name="Nome" tip="Aqui você preenche seu nome completo" />
                    </td>
                </tr>
                <tr>
                    <td>
                        E-mail
                    </td>
                    <td>
                        <input type="text" id="email" name="email" class="input_text required email" field_name="E-mail" tip="Utilize um e-mail válido" />
                    </td>
                </tr>
                <tr>
                    <td>
                        Assunto
                    </td>
                    <td>
                        <input type="text" id="assunto" name="assunto" class="input_text required" field_name="Assunto" tip="Assunto sobre o qual você quer atendimento" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        Mensagem
                        <br />
                        <textarea id="msg" name="msg" class="textarea required" field_name="Mensagem"></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" value="Enviar" />
                    </td>
                </tr>
            </table>
        </form>
        <script type="text/javascript">
            $(document).ready(function() {
                $.post(
                    "dic/pt/javascript.xml",
                    null,
                    function(xml){
                        xml_dic = xml;
                        form = new Form();
                        form.init('form_contact');
                    },
                    'xml'
                );                
            });
        </script>
    </body>
</html>
