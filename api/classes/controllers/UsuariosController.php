<?php    
    use \api\classes\controllers\ServerController;
    use \api\classes\models\UsuariosModel;
    
    class Usuarios extends ServerController {
        /**
        * Conteúdo do serviço com o métodos de Usuário
        */
        public function actionIndex(){
            try{
                $this->server->register("ShowString"                       
                 ,array('name'=>'xsd:string')
                 ,array('return'=>'xsd:string')
                 ,$this->namespace
                 ,$this->namespace . "/ShowString"
                 ,'rpc'
                 ,'encoded'
                 ,'Sample of embedded classes...' 
                );
                
                //Registra função excluiUsuario no serviço
//                $this->server->register("Usuarios.excluiUsuario", 
//                        array(
//                            'xmlParams' => 'xsd:string'
//                        ), // Descriçao dos parâmetros de entrada
//                        array('return' => 'xsd:string'),      // Descrição da saída
//                        'urn:superproweb',                    // namespace
//                        'urn:superproweb#excluiUsuario',          // soapaction
//                        'rpc',                                // style
//                        'encoded',                            // use
//                        'Função que exclui um usuário da escola logada no WS' // descrição do serviço
//                );
                
                //Saída do Server
                $HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ?
                $HTTP_RAW_POST_DATA : '';
                $this->server->service($HTTP_RAW_POST_DATA);
            }catch(Exception $e){
                $this->imprimirErro($e);
            }
        }
        
        /**
        * Função que exclui um usuário da escola logada no WS
        * 
        * @param string $xmlParams String com o XML dos campos de entrada
        * @return string $xmlResult
        */
       function excluiUsuario($xmlParams){
           try{
               $erro   = 0;
               $msg    = "Usuário excluido com sucesso!";
               $dados  = "";

               if($xmlParams){
                   $this->imprimirErro("Teste");
                   $xml = new SimpleXMLElement($xmlParams);

                   if($xml){
                       //Array de parâmetros
                       $params = $xml->params->param;

                       //Campos utilizados
                       $token      = $this->getXmlField($params, 'token');
                       $id_usuario = $this->getXmlField($params, 'id_usuario');

                       if(!isset($token) || $token == null || $token == ""){
                           $erro   = 4;
                           $msg    = "Token inválido!";
                       }else{
                           //Autentica usuário e token
                           $ret = $this->authenticate($token);

                           if(!$ret->status){
                               $erro   = $ret->erro;
                               $msg    = $ret->msg;
                           }else{
                               //Valida envio do id_usuario
                               if($id_usuario <= 0 || $id_usuario == $ret->ID_CLIENTE){
                                   if($id_usuario <= 0){
                                       $erro   = 91;
                                       $msg    = "ID_USUARIO inválido ou nulo!";
                                   }else{
                                       $erro   = 92;
                                       $msg    = "Sem permissão para excluir seu próprio usuário!";
                                   }
                               }else{
                                   //Model de usuários
                                   $mdUsuarios = new UsuariosModel();
                                   
                                   //Valida se o usuário é dependente do usuário logado
                                   $sql = "SELECT 
                                               ID_CLIENTE
                                           FROM
                                               SPRO_CLIENTE
                                           WHERE
                                               ID_MATRIZ = {$ret->ID_CLIENTE}
                                           AND
                                               ID_CLIENTE = {$id_usuario}
                                           LIMIT
                                               1
                                           ;";

                                   $mysql  = new Mysql();
                                   $rs     = $mysql->query($sql);

                                   if(mysql_num_rows($rs) <= 0){
                                       $erro   = 83;
                                       $msg    = "Usuário não é seu dependente ou não existe!";
                                   }else{
                                       
                                       $rs = $mdUsuarios->atualizaStatusUsuario($id_usuario, $ret->ID_CLIENTE, 'EXCLUIR');

                                       //VErifica se o status foi atualizado
                                       if(!$rs->status){
                                           $erro   = 84;
                                           $msg    = $rs->msg;
                                       }
                                   } //Verifica dependencia do usuário
                               } //Valida envio de id_usuario
                           }
                       }
                   }else{
                       $erro   = 6;
                       $msg    = "XML de entrada fora do padrão!";
                   }
               }else{
                   $erro   = 5;
                   $msg    = "XML Params inválido ou nulo!";
               }

               //$ret  = "<?xml version='1.0' encoding='UTF-8'>";
               $ret = "<root>";
               $ret .= "<status>";
               $ret .= "<erro>{$erro}</erro>";
               $ret .= "<msg>".  utf8_encode($msg)."</msg>";
               $ret .= "</status>";
               $ret .= $dados;
               $ret .= "</root>";

               return $ret;
           }catch(Exception $e){
               $ret = "<root>";
               $ret .= "<status>";
               $ret .= "<erro>1</erro>";
               $ret .= "<msg>". utf8_encode($e->getMessage()) ."</msg>";
               $ret .= "</status>";
               $ret .= "</root>";

               return $ret;
           }
       }
    }
    
    function ShowString($mens){

        return "\n##Remote Class :".__CLASS__."\n##Remote Method : ".__METHOD__."\n## mSG :{$mens}";

     }
?>
