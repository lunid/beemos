<?php
    namespace api\classes\controllers;
    
    //Include de Bibliotecas
    include "/api/nusoap/nusoap.php";

    //Depêndencias
    use \sys\classes\mvc\Controller;
    
    /**
    * Classe Controller usada com default para criação se um servidor SOAP.
    */
    class ServerController extends Controller {
        //Armazena o SERVER NuSoap criado no contrutor
        protected $server;
        
        /**
         * Cria um servidor WSDL (NuSoap) para utilização
         */
        public function __construct(){
            try{
                //Inicia servidor WSDL e suas configuirações
                $this->server = new \nusoap_server();
                $this->server->configureWSDL("SuperProWeb", "urn:superproweb");
            }catch(Exception $e){
                $this->imprimirErro($e);
            }
        }
        
        /**
         * Devolve o erro em forma XML
         * 
         * @param Exception $e
         * @return string XML com o padrão de erros
         */
        protected function imprimirErro($e){
            $ret = "<root>";
            $ret .= "<status>";
            $ret .= "<erro>1</erro>";
            $ret .= "<msg>". utf8_encode($e->getMessage()) ."</msg>";
            $ret .= "</status>";
            $ret .= "</root>";

            return $ret;
        }
        
        /**
        * Função que varre o array XML de parâmetros e retorna o valor do campo solicitado
        * 
        * @param array $xmlParams
        * @param string $fielName
        * 
        * @return string $value
        * @throws Exception
        */
       protected function getXmlField($xmlParams, $fielName){
           try{
               //Varre o array de campos para capturar o valor solicitado.
               if(is_object($xmlParams) || is_array($xmlParams)){
                   foreach($xmlParams as $param){
                       $id = (string)$param['id'];

                       if($id == $fielName){
                           $tmp = mysql_escape_string(trim((string)$param));

                           //Se depois das tratativas se o valor for vazio retorna NULL
                           if($tmp == '' || $tmp == null){
                               return null;
                           }else{
                               return mysql_escape_string(trim((string)$param));
                           }
                       }
                   }
               }

               return null;
           }catch(Exception $ex){
               throw $ex;
           }
       }
       
       /**
        * Realiza a autenticação do usuário e senha HTTP assim como o Token sendo utilizado no momento
        * 
        * @param string $token
        * @return \stdClass 
        */
       protected function authenticate($token){
           try{
               //Objeto de retorno
               $ret            = new stdClass();
               $ret->status    = false;
               $ret->erro      = 1;
               $ret->msg       = "Erro inesperado!";

               //Captura o Usuário e Senha enviados via HTTP - Basic (Base64)
               $server = new nusoap_server();
               $server->parse_http_headers();
               $server->headers;
               $values = explode(" ", $server->headers['authorization']);
               $dados  = explode(":", base64_decode($values[1]));

               $user = $dados[0];
               $pass = $dados[1];

               //Consulta usuário e senha na Base de dados
               $sql = "SELECT
                           WS.ID_WS_USUARIO,
                           WS.ID_CLIENTE,
                           C.BLOQ
                       FROM
                           SPRO_WS_USUARIO WS
                       INNER JOIN
                           SPRO_CLIENTE C ON C.ID_CLIENTE = WS.ID_CLIENTE
                       WHERE
                           WS.LOGIN = '" . mysql_escape_string($user) . "'
                       AND
                           WS.SENHA = '" . md5($pass) . "'
                       LIMIT
                           1
                       ";

               $mysql  = new Mysql();
               $rs     = $mysql->query($sql);

               if(mysql_num_rows($rs) <= 0){
                   $ret->erro  = 2;
                   $ret->msg   = "Falha na autenticacao HTTP - Usuário inválido!";
                   return $ret;
               }

               //Transforma o ResultSet em objeto
               $rs_user = mysql_fetch_object($rs);

               if($rs_user->BLOQ == 1){
                   $ret->erro  = 3;
                   $ret->msg   = "Falha na autenticacao HTTP - Usuário bloqueado!";
                   return $ret;
               }

               //Verificação do TOKEN enviado.
               if($token == null){
                   //Caso o $token seja NULL, um novo token é gerado

                   //Adquire um novo token para ser utilizado
                   return self::geraToken($rs_user->ID_CLIENTE, $user);
               }else{
                   //Caso o $token seja enviado o mesmo será validado      
                   return self::validaToken($token, $rs_user->ID_CLIENTE);
               }
           }catch(Exception $e){
               $ret            = new stdClass();
               $ret->status    = false;
               $ret->msg       = "Erro inesperado! - " . $e->getMessage();
               $ret->erro      = 1;
               return $ret;
           }
       }
       
       /**
        * Função que gera um novo TOKEN, sempre gerando um númeor único de acordo com
        * o banco de dados.
        * 
        * @return string $token
        * @throws Exception
        */
       static function geraToken($ID_CLIENTE, $LOGIN){
           try{
               $ret                = new stdClass();
               $ret->status        = false;
               $ret->erro          = 7;
               $ret->msg           = "Já existe um token ativo para o cliente!";
               $ret->ID_CLIENTE    = $ID_CLIENTE;

               $dt_inicio      = date("Y-m-d H:i:s");
               $dt_validade    = date("Y-m-d H:i:s", mktime(date('H'), date('i'), (date('s')+10)));

               $sql_time = "SELECT
                           ID_TOKEN
                       FROM
                           SPRO_TOKEN_ACESSO
                       WHERE
                           ID_CLIENTE = '" . $ID_CLIENTE . "' 
                       AND
                           DATA_REGISTRO BETWEEN '{$dt_inicio}' AND '{$dt_validade}'
                       LIMIT
                           1
                       ;";

               $mysql  = new Mysql();                        
               $rs     = $mysql->query($sql_time);

               if(mysql_num_rows($rs) > 0){
                   return $ret;
               }

               $count = 0;
               $ver   = false;

               do{
                   //Gera um novo Token
                   $token  = self::token();

                   //Verifica se já existe esse TOKEN na base de dados
                   $sql = "SELECT
                               ID_TOKEN
                           FROM
                               SPRO_TOKEN_ACESSO
                           WHERE
                               TOKEN = '" . $token . "'
                           LIMIT 
                               1
                           ;";

                   $rs = $mysql->query($sql);

                   //Se o TOKEN for válido ele sai do loop
                   if(mysql_num_rows($rs) <= 0){
                       $ver    = true;
                   }else{
                       $token  = null;
                   }

                   //Soma o contador, pois ele vai tentar gerar 10 novos tokens
                   $count++;
               }while(!$ver && $count <= 10);

               if($token == null){
                   throw new Exception("LOOP Infinito ao gerar Token");
               }

               //Grava o novo TOKEN no banco de dados
               $sql = "INSERT INTO
                           SPRO_TOKEN_ACESSO
                           (
                               ID_CLIENTE,
                               LOGIN,
                               TOKEN,
                               DATA_REGISTRO
                           )
                           VALUES
                           (
                               {$ID_CLIENTE},
                               '" . mysql_escape_string($LOGIN) . "',
                               '{$token}',
                               '{$dt_validade}'
                           );
                       ;";

               $mysql->query($sql);

               $ret->status    = true;
               $ret->erro      = 0;
               $ret->msg       = "Token gerado com sucesso!";
               $ret->token     = $token;

               return $ret;
           }catch(Exception $ex){
               throw $ex;
           }
       }

       /**
        * Função que verifica a valida do Token usado na requisição
        * 
        * @param string $TOKEN
        * @param int $ID_CLIENTE
        * @return \stdClass
        */
       static function validaToken($TOKEN, $ID_CLIENTE){
           try{
               $ret                = new stdClass();
               $ret->status        = false;
               $ret->erro          = 4;
               $ret->msg           = "Token inválido!";
               $ret->ID_CLIENTE    = $ID_CLIENTE;

               $dt_atual       = date("Y-m-d H:i:s");

               $sql = "SELECT
                           ID_TOKEN,
                           TOKEN
                       FROM
                           SPRO_TOKEN_ACESSO
                       WHERE
                           ID_CLIENTE = '" . $ID_CLIENTE . "' 
                       AND
                           DATA_REGISTRO >= '{$dt_atual}'
                       LIMIT
                           1
                       ;";

               $mysql  = new Mysql();
               $rs     = $mysql->query($sql);

               if(mysql_num_rows($rs) > 0){
                   $rs_tk = mysql_fetch_object($rs);

                   if($rs_tk->TOKEN == $TOKEN){
                       $ret->status    = true;
                       $ret->erro      = 0;
                       $ret->msg       = "Token válido!";
                   }
               }

               return $ret;
           }catch(Exception $ex){
               $ret            = new stdClass();
               $ret->status    = false;
               $ret->erro      = 1;
               $ret->msg       = "Erro inesperado! - " . $ex->getMessage();

               return $ret;
           }
       }
    }
?>
