<?
    require_once 'class/usuario.php';
    
    if($_POST){
        $usuario = new Usuario();
        
        $acao = strtolower(trim($_POST['acao']));
        
        switch ($acao) {
            case 'reenviar':
                $usuario->setEmail($_POST['email']);
                $ret = $usuario->reenviarSenha();
                break;
        }
        
        echo $ret['msg'] . "<br />";
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Formulário de Cadastro</title>
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
        <script type="text/javascript" src="js/libs/mask/mask.js"></script> 
        <script type="text/javascript" src="js/libs/util/dictionary.js"></script>
        <script type="text/javascript" src="js/libs/util/form.js"></script>
    </head>
    <body>
        <form id="form_reenviar_senha" name="form_reenviar_senha" action="form_cadastro.php" method="post" class="form" onsubmit="return form.validate(this);">
            <table align="left" cellpading="3" cellspacing="1" border="1">
                <tr>
                    <td>
                        E-mail
                    </td>
                    <td>
                        <input type="text" id="email" name="email" class="input_text required email" field_name="E-mail" tip="Utilize um e-mail válido" value="<? if(@$_POST['acao'] == 'reenviar' && @$ret['status'] > 0){ echo $_POST['email']; } ?>" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="hidden" id="acao" name="acao" value="reenviar" />
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
                        form    = new Form();
                        form.init('form_reenviar_senha');
                    },
                    'xml'
                );                
            });
        </script>
    </body>
</html>
