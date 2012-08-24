<?
    require_once 'class/visitante.php';
    
    if($_POST){
        $visitante = new Visitante();
        
        $visitante->setNome($_POST['nome']);
        $visitante->setEmail($_POST['email']);
        $visitante->setTelefone($_POST['telefone']);
        $visitante->setSocialId($_POST['rede_id'], $_POST['rede_name']);
        
        $ret = $visitante->save();
        
        echo $ret['msg'] . "<br />";
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Formulário de Suporte</title>
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
        <form id="form_visitante" name="form_visitante" action="form_visitante.php" method="post" class="form" onsubmit="return form.validate(this);">
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
                        Telefone
                    </td>
                    <td>
                        <input type="text" id="telefone" name="telefone" class="input_text phone" field_name="E-mail" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <a href="javascript:void(0);" onclick="window.open('rede.php?rede=google', 'LoginGoogle', 'width=1000,height=400,menubar=no,scrollbars=yes')">Login com Google</a>
                        <br />
                        <a href="javascript:void(0);" onclick="window.open('rede.php?rede=facebook', 'LoginFacebook', 'width=1000,height=400,menubar=no,scrollbars=yes')">Login com Facebook</a>
                        <br />
                        <input type="hidden" id="rede_name" name="rede_name" />
                        <input type="hidden" id="rede_id" name="rede_id" />
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
                        form.init('form_visitante');
                    },
                    'xml'
                );                
            });
        </script>
    </body>
</html>
