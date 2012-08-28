<?
    //====================================================================================================================//
    //======================================== Valida Acesso do Usuário na Página ========================================//
    //====================================================================================================================//
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
    //====================================================================================================================//
    //======================================== Valida Acesso do Usuário na Página ========================================//
    //====================================================================================================================//
    
    //====================================================================================================================//
    //========================================== Início dos controles da Página ==========================================//
    //====================================================================================================================//
    
    //ID do usuário VAZIO
    $id_usuario = 0;
    
    //Valida POST
    if($_POST){
        $usuario = new Usuario();
        
        if(@(int)$_POST['id_usuario'] > 0){
            $usuario->setIdUsuario((int)$_POST['id_usuario']);
            $id_usuario = (int)$_POST['id_usuario'];
        }
        
        $usuario->setNome       ($_POST['nome']);
        $usuario->setEmail      ($_POST['email']);
        $usuario->setSenha      ($_POST['senha']);
        $usuario->setIdPerfil   ($_POST['id_perfil']);
        
        $rs = $usuario->save();
        
        echo $rs->msg . "<br /><br />";
    }
    
    if((int)@$_GET['id_usuario'] > 0){
        $id_usuario = (int)$_GET['id_usuario'];
        
        $usuario = new Usuario();
        
        if(!$usuario->carregaUsuario((int)$id_usuario)){
            echo "Falha ao carregar Usuário - ID não encontrado.<br /><br />";
        }
    }
    
    if($usuario == NULL){
        $usuario = new Usuario();
    }
    
    //Lista perfis para o select do Fomr
    $perfis = new Perfil();
    $perfis = $perfis->carregaPerfis();
    
    //====================================================================================================================//
    //=========================================== Fim dos controles da Página ============================================//
    //====================================================================================================================//
?>
<!DOCTYPE html>
<html>
    <head>
        <title>ADM Interbits | Usuários | <?  if($id_usuario > 0){ ?> Editar <? }else{ ?> Novo <? } ?></title>
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
                        <input type="text" name="nome" id="nome" field_name="Nome" class="input_text required" value="<? if(!@$rs->status || $id_usuario > 0){ echo @$usuario->getNome(); } ?>" />
                    </td>
                </tr>
                <tr>
                    <td>
                        E-mail
                    </td>
                    <td>
                        <input type="text" name="email" id="email" field_name="E-mail" class="input_text required email" value="<? if(!@$rs->status || $id_usuario > 0){ echo @$usuario->getEmail(); } ?>" <? if($id_usuario > 0){ ?> readonly="readonly" style="background-color:#DADADA;" <? } ?> />
                    </td>
                </tr>
                <tr>
                    <td>
                        Perfil
                    </td>
                    <td>
                        <select id="id_perfil" name="id_perfil" field_name="Perfil" class="required">
                            <option value="0">Selecione um perfil</option>
                            <? 
                                if(sizeof($perfis) > 0){ 
                                    foreach($perfis as $row){
                                        if($row->STATUS == 1){
                                            if((!@$rs->status || $id_usuario > 0) && $row->ID_PERFIL == @$usuario->getIdPerfil()){
                                                $selected = "selected='selected'";
                                            }else{
                                                $selected = "";
                                            }
                            ?>
                                            <option value="<?=$row->ID_PERFIL?>" <?=$selected?>><?=$row->DESCRICAO?></option>
                            <?
                                        }
                                    }
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <? if($id_usuario > 0){ ?>
                <tr>
                    <td colspan="2">
                        Só preencha o campo Senha caso queira alterá-la
                    </td>
                </tr>
                <? } ?>
                <tr>
                    <td>
                        Senha
                    </td>
                    <td>
                        <input type="password" name="senha" id="senha" field_name="Senha" class="input_text <? if($id_usuario == 0){ ?> required <? } ?>" />
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
                        <?  if($id_usuario > 0){ ?> <input type="hidden" id="id_usuario" name="id_usuario" value="<?=$id_usuario?>" /> <? } ?>
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
