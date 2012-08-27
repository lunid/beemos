<!--
To change this template, choose Tools | Templates
and open the template in the editor.



-->
<!DOCTYPE html>
<html>
    <head>
        <title>ADM Interbits</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <script type="text/javascript" src="../js/libs/jquery_171.js"></script>
        <script type="text/javascript" src="../js/libs/tooltip/jquery_qtip_100_min.js"></script> 
        <script type="text/javascript" src="../js/libs/mask/mask.js"></script> 
        <script type="text/javascript" src="../js/libs/util/dictionary.js"></script>
        <script type="text/javascript" src="../js/libs/util/form.js"></script>
    </head>
    <body>
        <form id="adm_form_login" name="adm_form_login" method="post" action="index.php" class="form" onsubmit="return form.validate(this);">
        <table>
            <tr>
                <td>
                    Login
                </td>
                <td>
                    <input type="text" id="adm_login" name="adm_login" class="input_text required" field_name="Login" />
                </td>
            </tr>
            <tr>
                <td>
                    Senha
                </td>
                <td>
                    <input type="password" id="adm_senha" name="adm_senha" class="input_text required" field_name="Senha" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" value="login" />
                </td>
            </tr>
        </table>
        </form>
        <script type="text/javascript">
            $(document).ready(function() {
                $.post(
                    "../dic/pt/javascript.xml",
                    null,
                    function(xml){
                        xml_dic = xml;
                        form    = new Form();
                        form.init('adm_form_login');
                    },
                    'xml'
                );                
            });
        </script>
    </body>
</html>
