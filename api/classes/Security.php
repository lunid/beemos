<?php
    namespace api\classes;
    
    use \api\classes\models\ServerModel;
    
    /**
     * Classe para validações e geração de códigos de segurança
     */
    class Security {

        /**
         * Função que gera um novo TOKEN, sempre gerando um número único de acordo com
         * o banco de dados.
         * 
         * @return string $token
         * @throws Exception
         */
        public static function gerarToken($idCliente, $login){
            try{
                //Obejto de retorno
                $ret                = new \stdClass();
                $ret->status        = false;
                $ret->erro          = 7;
                $ret->msg           = "Já existe um token ativo para o cliente!";
                $ret->ID_CLIENTE    = $idCliente;

                //Model do Servidor
                $mdServer = new ServerModel();
                
                //Valida existência de Token
                if($mdServer->verificarTokenCliente($idCliente)){
                    return $ret;
                }

                $count = 0;
                $ver   = false;

                do{
                    //Gera um novo Token
                    $token  = self::token();

                    //Se o TOKEN for válido ele sai do loop
                    if($mdServer->validarToken($token)){
                        $ver    = true; //Finaliza loop
                    }else{
                        $token  = null; //Continua o loop
                    }

                    //Soma o contador, pois ele vai tentar gerar 10 novos tokens
                    $count++;
                }while(!$ver && $count <= 10);
                
                //Verifica se houve sucesso no loop
                if($token == null){
                    throw new Exception("LOOP não gerou um novo Token");
                }
                
                //Grava o novo TOKEN no banco de dados
                if(!$mdServer->salvarTokenCliente($idCliente, $login, $token)){
                    throw new Exception("Falha ao inserir novo Token");
                }
                
                //Retorno OK
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
        public static function validarToken($idCliente, $token){
            try{
                //Objeto de retorno
                $ret                = new \stdClass();
                $ret->status        = false;
                $ret->erro          = 4;
                $ret->msg           = "Token inválido!";
                $ret->ID_CLIENTE    = $idCliente;
                
                //Model Server
                $mdServer = new ServerModel();
                
                //Valida Token enviado
                if($mdServer->validarTokenAtivoCliente($idCliente, $token)){
                    //Retorno OK se Tpken válido
                    $ret->status    = true;
                    $ret->erro      = 0;
                    $ret->msg       = "Token válido!";
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

        /**
         * Gera um token de acesso para a solicitação
         * 
         * @return String $token
         * 
         * @throws Exception
         */
        public static function token() {
            try{
                //Gera um novo hash de token
                $token = self::geraHashId();
                return $token;
            }catch (Exception $e){
                throw $e;
            }
        }

        /**
         * Função que gera Hashs randõmicos e aleatórios como Token
         * 
         * @param int $id
         * @param int $length
         * @return string $hash
         * 
         * @throws Exception
         */
        public static function geraHashId() {
            try{
                //GERA UM HASH RAND�MICO
                $salt = date('dYmHsi');

                //TOKEN VIA CELULAR (MUDA DE 30 EM 30 SEGUNDOS)
                $dymh   = date('dYmH');
                $i      = date('i'); //MINUTOS
                $seg    = date('s');
                $s      = (int) $seg;
                if ($s > 0 && ($s % 30) != 0) {
                    $seg = ($s > 30) ? 30 : '00';
                }
                $salt   = $dymh . $seg . $i;

                $id     = uniqid(hash("sha512", rand()), TRUE);
                $code   = hash("sha512", $id . $salt);

                return substr($code, 0, 128);
            }catch (Exception $e){
                throw $e;
            }
        }

        /**
         * Criptografa uma string usando md5.
         * @param type $string
         * @return type 
         */
        public static function md5_salt($string) {
            //CRIPTOGRAFIA DE UMA STRING
            $chars = str_split('~`!@#$%^&*()[]{}-_\/|\'";:,.+=<>?');
            $keys = array_rand($chars, 6);

            foreach ($keys as $key) {
                $hash['salt'][] = $chars[$key];
            }

            $hash['salt'] = implode('', $hash['salt']);
            $hash['salt'] = md5($hash['salt']);
            $hash['string'] = md5($hash['salt'] . $string . $hash['salt']);
            return $hash;
        }



        public static function checkToken($var) {
            //RECEBE UMA VARI�VEL E COMPARA COM O �LTIMO TOKEN GERADO
            $var = trim($var);
            $token = trim(Request::session('GL_TOKEN'));
            unset($_SESSION['GL_TOKEN']); //DESTR�I O TOKEN
            $tamVar = strlen($var);
            //die("$var - $token");
            //COMPARA (CASE SENSITIVE) SE $var � IGUAL AO $token
            if ($tamVar >= 32) {
                if (strcmp($var, $token) == 0) {
                    return true;
                } else {
                    echo "ERRO: $var - $token";
                    return false;
                }
            } else {
                return false;
            }
            if ($tamVar >= 32 && strcmp($var, $token) == 0)
                return true;
            return false;
        }

        function readToken($token) {
            //VERIFICA SE UM TOKEN SOLICITADO � V�LIDO
            $tokens = file('./tokens.txt');
            if (in_array($token, $tokens)) {
                //O TOKEN INFORMADO FOI ENCONTRADO
                return true;
            }
            return false;
        }

        public static function geraPasswd($tipo = "L L N N L L N N") {
            //GERA UMA SENHA ALEAT�RIA COM 8 CARACTERES (L = LETRA, N = N�MERO)
            $tipo = explode(" ", $tipo);

            //Cria��o de um padr�o de letras mai�sculas e n�meros
            $padrao_letras = "A|B|C|D|E|F|G|H|I|J|K|L|M|N|O|P|Q|R|S|T|U|V|X|W|Y|Z";
            $padrao_numeros = "0|1|2|3|4|5|6|7|8|9";

            //Criando os arrays, que armazenar�o letras e n�meros
            //O explode retira os separadores | para utilizar as letras e n�meros
            $array_letras = explode("|", $padrao_letras);
            $array_numeros = explode("|", $padrao_numeros);

            //Cria a senha baseado nas informa��es da fun��o (L para letras e N para n�meros)
            $passwd = "";
            $tam = sizeof($tipo);
            for ($i = 0; $i < $tam; $i++) {
                if ($tipo[$i] == "L" || $tipo[$i] == 'l') {
                    $letra = $array_letras[array_rand($array_letras, 1)];
                    if ($tipo[$i] == 'l')
                        $letra = strtolower($letra);
                    $passwd.= $letra;
                } else {
                    if ($tipo[$i] == "N") {
                        $passwd.= $array_numeros[array_rand($array_numeros, 1)];
                    }
                }
            }
            return $passwd;
        }

    }
?>