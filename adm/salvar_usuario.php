<?
    session_start();
    
    require_once 'class/usuario.php';
    
    if(isset($_SESSION['ADM_USUARIO'])){
        //Valida permissão de acesso ao Usuário
        $ret = Usuario::validaAcesso(unserialize($_SESSION['ADM_USUARIO']));
        
        if(!$ret->status){
            //redirecionando para efetuar login
            header("Location: index.php");  
        }
    }else{
        //redirecionando para efetuar login
        header("Location: index.php");
    }
    
    $perfis = new Perfil();
    $perfis->carregaPerfis();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>ADM Interbits | Usuários | Novo</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <script type="text/javascript" src="../js/libs/jquery_171.js"></script>
        <script type="text/javascript" src="../js/libs/tooltip/jquery_qtip_100_min.js"></script> 
        <script type="text/javascript" src="../js/libs/util/dictionary.js"></script>
        <script type="text/javascript" src="../js/libs/util/form.js"></script>
    </head>
    <body>
        <h1>
            Novo Usuário
        </h1>
        <form id="form_salvar_usuario" name="form_salvar_usuario" method="post" action="salvar_usuario.php" onsubmit="return form.validate(this);">
            <table align="center" border="1" cellpadding="2" cellspacing="2">
                <tr>
                    <td>
                        Nome
                    </td>
                    <td>
                        <input type="text" name="nome" id="nome" field_name="Nome" class="input_text required" />
                    </td>
                </tr>
                <tr>
                    <td>
                        E-mail
                    </td>
                    <td>
                        <input type="text" name="email" id="email" field_name="E-mail" class="input_text required email" />
                    </td>
                </tr>
                <tr>
                    <td>
                        Perfil
                    </td>
                    <td>
                        <select id="id_perfil" name="id_perfil" field_name="Perfil" class="required">
                            <option value="0">Selecione um perfil</option>
                            <option value="1">Administrador</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        Senha
                    </td>
                    <td>
                        <input type="password" name="senha" id="senha" field_name="Senha" class="input_text required" />
                    </td>
                </tr>
                <tr>
                    <td>
                        Repetir Senha
                    </td>
                    <td>
                        <input type="password" name="re_senha" id="re_senha" field_name="Repetir Senha" class="input_text repeat" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" value="Salvar" />
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
                        form.init('form_salvar_usuario');
                    },
                    'xml'
                );                
            });
        </script>
    </body>
</html>
