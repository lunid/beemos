<?php

namespace sys\classes\db;
use \sys\classes\util\Cache;
use \sys\classes\util\Request;
use \sys\classes\util\Dic;


/**
 * Classe de ORM (Mapeamento objeto relacional) para mySql.
 * 
 * A biblioteca utilizada para dar suporte à montagem e execução do SQL é a 
 * meekrodb ({@link http://www.meekrodb.com}).
 * Cada tabela deve ter uma classe filha de ORM.php. Exemplo: 
 * class Cliente extends ORM {
 *      ...
 * }
 * 
 * Por padrão, todas as tabelas possuem seu nome em caixa alta e contém um prefixo.
 * Por exemplo: SPRO_CLIENTE, SPRO_CREDITO_CONSOLIDADO etc.
 * O nome da classe que repsenta a tabela deve ser formado por inicial maiúscula
 * e as demais letras minúsuculas, porém sem prefixo (veja a propriedade $prefixoTable).
 * 
 * Nomes compostos devem ser intercalados com inicial maiúscula em cada palavra.
 * Exemplo: <b>SPRO_CLIENTE</b> será <b>Cliente.php</b>, <b>SPRO_CREDITO_CONSOLIDADO</b> será <b>CreditoConsolidado.php</b>.
 * 
 * @author Claudio Rubens Silva Filho <claudio@supervip.com.br> 
 * @package api\db
 */
abstract class ORM { 
    /**
     * Método que retorna o nome da tabela do DB composto de prefixo e em caixa alta.
     * O nome da tabela formado pelo nome da classe usando o 
     * caracter "_" como seprador das palavras iniciadas em letra maiúscula.
     * Exemplo: CampanhaIndicaAmigo, ficaria CAMPANHA_INDICA_AMIGO.
     * @param string $className Nome da classe
     * @return string
     */
    private $arrColumns;   
    private $prefixoTable          = 'SPRO_';
    private $arrKey                = array(); //Array que guarda a chave primária da tabela
    private $arrUnique             = array();
    private $colAutoNum; //Coluna autoNumber
    private $tableName;
    private $arrCols               = array(); //Guarda um array associativo cujo índice é o nome de cada coluna.
    private $arrParams             = array(); //Array utilizado para fazer insert/update na tabela.
    private $id                    = 0;//ID do registro quando for ação de UPDATE ou DELETE
    private $row                   = array();//Array associativo que guarda os dados de um registro
    private $where                 = NULL;
    protected $alias               = '';//Variável de suporte ao criar JOIN
    protected $orderBy             = NULL;
    private $limit                 = ''; //Quando definido, guarda o intervalo de LIMIT no formato $posIni,$posFim
    protected static $results      = array();//Guarda o resultado de uma consulta do tipo SELECT 
    protected $joinWhere           = '';//Variável auxiliar para criação de JOIN   
    private $arrObjJoin            = array();//Array dos objetos Table usados no JOIN
    protected $fieldsJoin          = '';//String com campos usados no JOIN (a vírgula deve ser o separador de cada campo)
    private static $debug          = FALSE;//Se TRUE, imprime as strings SQL com echo
    
    /**
     * Construtor da classe.
     * 
     * Para iniciar um objeto localizando o registro pelo seu ID:
     * <code>
     *  $objCliente = new Cliente(10);//Carrega os dados do registro cujo ID = 10
     *  echo $objCliente->NOME;//Imprime o campo NOME do registro atual.
     * </code>
     * 
     * @param integer $id Opcional.
     */
    function __construct($id=0){
        $this->init();
        if ((int)$id > 0) $this->findAutoNum($id);
    }
    
    /**
     * Ativa a opção de debug (IMPORTANTE: não execute essa opção em ambiente de produção).
     * 
     * Este recurso é útil para localizar possíveis problemas em consultas SQL.
     * Ao ativá-la as consultas SQL serão impressas na tela.
     * 
     */
    protected static function debugOn(){
        self::$debug = TRUE;
    }
    
    /**
     * Desativa a opção de debug. 
     */
    protected static function debugOff(){
        self::$debug = FALSE;
    }
    
    /**
     * Inicializa os dados da tabela atual (nome da tabela, nome e atributos 
     * de cada coluna, campo(s) de chave primária).
     * 
     * O nome da tabela deve seguir as regras do DB
     * (prefixo + caixa alta + underline como separador de nome composto).
     * Os atributos de cada coluna são guardados na variável string[] $arrCols, onde o
     * índice é associativo (nome da coluna) e seu valor é uma string formada pelos
     * seus respectivos atributos separados por barra normal (/), seguindoo formato
     * ($colName/$type/$null/$key/$extra).
     * 
     * @return void
     *  
     */
    private function init(){
        $this->getTable();
        $results    = $this->loadColumns();
        if (is_array($results)) {
            foreach($results as $col){
                $colName                    = $col['Field'];//NOME DA COLUNA
                $type                       = $col['Type'];//Ex: int(11), char(100)...
                $null                       = $col['Null'];//NO | YES
                $key                        = $col['Key'];//Ex: PRI (chave primária) | UNI (campo único) | MUL (chave composta)
                $extra                      = $col['Extra'];//Ex: auto_increment
                $this->arrCols[$colName]    = $colName.'/'.$type.'/'.$null.'/'.$key.'/'.$extra;
                if ($key == 'PRI') {
                    $this->arrKey[] = $colName;                   
                } elseif ($key == 'UNI') {
                    $this->arrUnique[] = $colName;
                }
                if ($extra == 'auto_increment') $this->colAutoNum = $colName;
            }
        }        
    }
    
    /**
     * Faz a leitura das colunas da tabela atual.
     * 
     * Após a primeira leitura guarda em cache para reutilização na sessão do usuário.
     *
     * @return mixed[] Array multidimensional com dados de cada coluna da tabela.     
     */    
    private function loadColumns(){
        $tableName              = $this->getTable();
        $varCache               = 'COLS_'.$tableName;
        $results                = Request::session($varCache,'ARRAY');
        if (count($results) == 0 || 1==1) {
            //$out        = Cache::getVarMemCache($varCache);        
            $sql                    = 'SHOW COLUMNS FROM '.$tableName;
            $results                = \DB::query($sql);        
            $_SESSION[$varCache]    = $results;
        }
       $this->arrColumns = $results;
       return $results;
    } 
    
    /**
     * Retorna o alias da tabela. Método auxiliar de setJoin().
     * 
     * Caso o atributo $alias não esteja definido, retorna o nome da tabela.
     *
     * @return string
     */    
    private function getAlias(){
        $table      = $this->getTable();
        $alias      = $this->alias;
        $alias      = (strlen($alias) > 0)?$alias:$table;
        return $alias;
    }
    
    /**
     * Retorna o nome da classe atual sem seu namespace.
     * 
     * @return string
     */       
    private static function getClassItem(){
        $classItem  = self::$classItem;
        $arrClass   = explode('\\',$classItem);
        $className  = $arrClass[count($arrClass)-1];        
        return $className;            
    }
    
    /**
     * Método responsável por centralizar e executar todas as ações SQL
     * 
     * Utiliza a biblioteca meekrodb ({@link http://www.meekrodb.com}).
     * 
     * SELECT : retorna um mixed[] multidimensional.
     * INSERT | INSERT_UPDATE: retorna um int com o ID do registro cadastrado/alterado.
     * DELETE: void
     * FIRST_ROW: mixed Retorna o valor de uma coluna (int | string | float etc).
     * FIRST_FIELD: mixed[] Retorna um array associativo unidimensional com as colunas de um único registro.*
     *
     * @param string $stmt indica qual ação deve ser executada. Valores possíveis:
     * SELECT | INSERT | UPDATE | DELETE | FIRST_ROW | FIRST_FIELD | INSERT_UPDATE
     * 
     * @param string[] $args Opcional. Array com os argumentos esperados na ação solicitada.
     * @return mixed O método retorna um tipo específico de acordo com a ação solicitada:
     * 
     * @throws \Exception [ARGS_NULL] Se argumentos informados forem inválidos para a ação solicitada (parâmetro $stmt).
     */    
    private function exec($stmt='SELECT',$args=NULL){
        $table      = $this->getTable();
        $arrParams  = $this->arrParams;
        $argsFunc   = NULL;
        $out        = NULL;
        $msgErr     = $whereFields = $whereValues = '';
        $codMsgErr  = 'ARGS_NULL';
        $err        = FALSE;
        
        if (!is_string($stmt)){
           $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'STMT_PARAM_NOT_IS_STRING'); 
           throw new \Exception( $msgErr );        
        } elseif (!is_array($args) && ($stmt == 'UPDATE' || $stmt == 'DELETE')){
           $err = TRUE;
        } elseif ($stmt == 'UPDATE' || $stmt == 'DELETE') {            
            if (!is_array($args)) {
                $err = TRUE;        
            } else {
                $argsFunc[] = $table;
                if ($stmt == 'UPDATE') $argsFunc[] = $arrParams;                
                $argsFunc   = array_merge($argsFunc,$args);     
            }
        } elseif ($stmt == 'FIRST_ROW' || $stmt == 'FIRST_FIELD'){
            $sql            = (isset($args['SQL']))?$args['SQL']:'';
            $whereValues    = (isset($args['WHERE_VALUES']))?$args['WHERE_VALUES']:'';            
            if (strlen($sql) == 0) $err = TRUE;;
        }
        
        if ($err){
            //Um erro foi localizado. Captura a mensagem que será disparada.
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,$codMsgErr);  
            if (is_string($stmt)) $msgErr = str_replace('{ACTION}',$stmt,$msgErr);            
        }
        
        \DB::$error_handler             = false;
        \DB::$throw_exception_on_error  = true;   
        
        try {
            switch(strtoupper($stmt)){
                case 'INSERT':                
                    \DB::insert($table,$arrParams); 
                    $out = $id = (int)\DB::insertId();
                    if ($id == 0) $msgErr = Dic::loadMsg(__CLASS__,'insert',__NAMESPACE__,'ID_ZERO');  
                    break;
                case 'UPDATE':
                    if (strlen($msgErr) == 0){
                        call_user_func_array('DB::update', $argsFunc);
                        $out = \DB::affectedRows();
                    }
                    break;
                case 'DELETE':
                    //Faz a exclusão do registro. Retorna o total de linhas afetadas.
                    if (strlen($msgErr) == 0){
                        call_user_func_array('\DB::delete', $argsFunc);
                        $out = \DB::affectedRows();
                    }
                    break;
                case 'FIRST_ROW':
                    //Retorna um array associativo unidimensional com os dados do registro solicitado.
                    $argsFunc       = array($sql,$whereValues);  
                    $out            = call_user_func_array('\DB::queryFirstRow', $argsFunc);
                    break;                
                case 'FIRST_FIELD':
                    //Retorna o valor de um campo único. Exemplo: Count(*).
                    $argsFunc   = array($sql,$whereValues);
                    $out        = call_user_func_array('\DB::queryFirstField', $argsFunc);                 
                case 'INSERT_UPDATE':                    
                    //Retorna o ID do registro cadastrado/alterado.
                    \DB::insertUpdate($table,$arrParams); 
                    $out = $id = (int)\DB::insertId();
                    break;
                default:
                    //SELECT: retorna um array bidimensional com todos os registros localizados no SELECT.
                    $limit = $this->limit;
                    $sql = (isset($args['SQL']))?$args['SQL']:'';
                    if (strlen($sql) > 0 && strlen($limit) > 0) $sql .= " LIMIT ".$limit;
                    $out = self::query($sql);
            }    
        } catch(\MeekroDBException $e){
            $msgErr     = "Erro mySql: " . $e->getMessage() . "<br>\n";
            $msgErr     .= "SQL Query: " . $e->getQuery() . "<br>\n"; // INSERT INTO accounts...            
        }
        
        if (self::$debug) echo \DB::getQuery();
        \DB::$error_handler = 'meekrodb_error_handler';
        \DB::$throw_exception_on_error = false;
        if (strlen($msgErr) > 0) throw new \Exception( $msgErr );        
        return $out;
    }
    
    /**
     * Executa uma string sql e retorna um array contendo os registros encontrados.
     * 
     * Exemplo - retorna os 10 primeiros registros da tabela SPRO_CLIENTE:
     * <code>
     *  $sql        = "SELECT NOME,EMAIL FROM SPRO_CLIENTE LIMIT 10";
     *  $results    = ORM::query($sql);
     *  if ($results !== FALSE) {
     *      foreach($results as $row){
     *         echo $row['EMAIL'].'<BR>';//Imprime o campo EMAIL de cada registro
     *      }
     *  }
     * </code>
     * 
     * @param string $sql Obrigatório.
     * @return mixed[] | FALSE <br>
     * Retorna um array bidimensional com os registros encontrados, 
     * ou FALSE caso nenhum registro tenha sido localizado.
     * 
     * @throws \Exception [SQL_NULL] Caso o parâmetro $sql seja nulo ou vazio.
     */    
    public static function query($sql){
        if (is_null($sql) || strlen($sql) == 0) {
            $msgErr = Dic::loadMsg(__CLASS__,NULL,__NAMESPACE__,'SQL_NULL');  
        } else {      
            if (self::$debug) echo $sql.'</br></br>';
            $results    = self::$results = \DB::query($sql);//Retorna um array bidimensional
            return $results;            
        }
        
        if (strlen($msgErr) > 0) throw new \Exception( $msgErr ); 
        return FALSE;
    } 
    
    public static function queryOneCol($sql){
        $out        = '';
        $results    = self::query($sql);        
        if (is_array($results) && count($results) > 0){
            $results    = array_values($results[0]);
            $out        = $results[0]; 
        }
        return $out;
    }
    
    /**
     * Define uma cláusula ORDER BY na consulta. 
     * 
     * Este método deve ser utilizado apenas com findAll() ou setJoin()
     * 
     * Exemplo:
     * <code>
     * $obj    = new Cliente();                    
     * $obj->setOrderBy('DATA_REGISTRO DESC');
     * $results = $obj->findAll('ID_INDICA_AMIGO <= 20');//Retorna cada registro como objeto da classe  
     * ...
     * </code>
     * 
     * @param string $orderBy 
     * @return void
     */
    function setOrderBy($orderBy){
        //Retira a cláusula 'ORDER BY' caso tenha sido (erroneamente) informada no parâmetro recebido.
        if (strlen($orderBy) > 0){
            $orderBy = str_replace('ORDER BY','',$orderBy);
            $orderBy = str_replace('order by','',$orderBy);        
            $this->orderBy = $orderBy;
        }
    }
    
    /**
     * Define o limite de registros de uma consulta.
     * 
     * Exemplo 1:<br>
     * Retorna os primeiros 20 registros da tabela SPRO_CLIENTE (classe Cliente.php)
     * ordenados por data de registro descrescente (DATA_REGISTRO DESC) e que 
     * sejam Pessoa Jurídica (PJ).
     * <code>
     * $obj = new Cliente();
     * $obj->setLimit(20);//Deve retornar os primeiros 20 regsitros
     * $obj->setOrderBy('DATA_REGISTRO DESC');//Ordem descendente de data
     * $results = $obj->findAll(PF_PJ = 'PJ');//Apenas registros 'PJ'    
     * if (is_array($results)){
     *    foreach($results as $var=>$objCliente){
     *         ...
     *    }                    
     * }
     * </code>
     * 
     * Exemplo 2:<br>
     * Retorna dados iniciando na posição 50 até 100.
     * <code>
     * $obj = new Cliente();
     * $obj->setLimit(100,50);//Deve retornar 50 registros iniciando na posição 50.
     * </code>
     * 
     * @param integer $posFim Posição final
     * @param integer $posIni Posição inicial
     * @return void
     */
    function setLimit($posFim,$posIni=1){
        if ($posFim > 1){
            $this->limit = $posIni.','.$posFim;
        }        
    }
    
    /**
     * Concatena as propriedades $orderBy e $limit à string $sql informada.
     * 
     * Estas propriedades são opcionais. Por isso, caso não tenham sido definidas
     * a string retornada é a mesma passada no parâmetro $sql;
     * 
     * @param string $sql Obrigatório. A string sql que deverá ser completada com $orderBy e $limit
     * @return string 
     */    
    private function concatOrderByLimit($sql){
        if (is_string($sql) && strlen($sql) > 0){
            $limit = $this->limit;                                              
            $sql   .= (!is_null($this->orderBy))?' ORDER BY '.$this->orderBy:'';
            if (strlen($limit) > 0) $sql .= " LIMIT ".$limit;           
        } else {
            $sql ='';
        }
        return $sql;
    }  
    
    /**
     * Faz o SELECT na tabela atual e retorna um array de objetos.
     * 
     * O exemplo abaixo imprime o campo EMAIL de cada registro da tabela Cliente. 
     * Note que cada registro é um objeto da classe Cliente:
     * 
     * Exemplo 1 - sem uso de parâmetros:
     *<code>          
     * $objCliente = new Cliente();
     * $results    = $objCliente->findAll(); // Retorna todos os registros da tabela.
     * foreach($results as $obj){
     *      echo $obj->EMAIL;//Objetos da classe Cliente
     * }
     * </code>      
     * 
     * Exemplo 2 - Com uso de parâmetros.<br>
     * Neste exemplo vamos localizar todos 
     * os registros Pessoa Física(PF) e que contenham 'Maria' no nome:
     * 
     * <code>     
     * $objCliente  = new Cliente();
     * $where       = "(PF_PJ='PF' AND NOME LIKE '%Maria%')";
     * $results     = $objCliente->findAll($where);
     * foreach($results as $obj){
     *      echo $obj->EMAIL;//Objetos da classe Cliente
     * }
     * </code>
     * 
     * @param string $where Opcional. Pode conter uma cláusula WHERE (ex: CAMPO1 = VALOR1 AND CAMPO2 = VALOR2)
     * @return object[]
     */    
    function findAll($where=''){
        if (strlen($where) == 0) $where = '1=1';
        $sql                = "SELECT * FROM ".$this->tableName." WHERE ".$where;
        $sql                = $this->concatOrderByLimit($sql);  
        $results            = self::query($sql);//Cada registro é um objeto
        $arrObj             = array();
        
        if (is_array($results) && count($results) > 0) {
            //Converte cada registro em objeto da classe atual
            $this->row  = array();//Zera a linha de um único registro caso esteja preenchida.
            $arrObj     = array_map(array($this, 'getObj'), $results);                       
        }        
        $rsObj = new Resultset($arrObj);  
        return $rsObj;
    }
    
    /**
     * Recebe os dados de um registro ($row) e 
     * converte em um objeto da classe atual (entidade de uma tabela).
     * 
     * @param mixed[] $row Obrigatório.
     * @return Object | FALSE Retorna um objeto da classe atual, ou FALSE caso o parâmetro não seja um array.
     */        
    private function getObj($row){                   
        $class = get_class($this);//Guarda o nome qualificado da classe atual (incluindo namespace)         
        if (is_array($row) && strlen($class) > 0){ 
            $obj        = new $class;
            $colAutoNum = $this->colAutoNum;//Coluna AutoNumber da tabela atual
            $id         = (int)$row[$colAutoNum];//ID AutoNumber do registro atual           
            if ($id > 0 && is_object($obj)) {
                $obj->findAutoNum($id);  
                return $obj;
            }
        } else {
            return false;
        }        
    } 
    
    /**
     * Conta o total de registros encontrados após uma operação de SELECT.
     * 
     * Exemplo:
     * <code>
     *  $objCliente = new Cliente();
     * 
     *  //Retorna todos os registros PF da tabela cliente:
     *  $results    = $objCliente->findAll("PF_PJ = 'PF'"); 
     *  
     *  //Imprime o total de registros encontrados:
     *  echo $objCliente->count();
     * </code>
     * 
     * Por exemplo, após chamar o método $obj->findAll() é possível saber quantos 
     * registros foram retornados executando o método $obj->count().
     * @return integer
     */    
    function count(){
        $results    = self::$results;
        $count      = (is_array($results))?count($results):0; 
        return $count;
    }
    
    /**
     * Localiza um registro a partir de seu campo AUTO_NUMBER.
     *      
     * Exemplo 1:
     * <code>
     * //Localiza um registro pelo seu ID (campo autoNumber):
     *  $objCliente = new Cliente();     
     *  $row        = $objCliente->findAutoNum(20); 
     * 
     *  if ($row !== false) {
     *      print_r($row);//Campos do registro localizado.
     *  } else {
     *      echo 'Nenhum registro foi localizado';
     *  }          
     * </code>
     * 
     * Exemplo 2:<br>
     * Ao iniciar um objeto (new...) fornecendo um ID de registro válido o método findAutoNum() é 
     * automaticamente chamado.
     * <code>
     *  $objCliente = new Cliente(10);//Carrega o registro cujo ID na tabela é 10.
     *  echo $objCliente->EMAIL;//Imprime o campo EMAIL do registro atual.
     * </code>
     * 
     * @param int $id Obrigatório. ID do registro a ser localizado pelo seu campo AutoNumber.
     * @return FALSE | mixed[] Retorna um array unidimensional associativo com os dados do registro.
     * Se não localizar o registro retorna FALSE. 
     * 
     * @throws \Execption [ID_ZERO] Caso o parâmetro $id seja zero ou não numérico. 
     * @throws \Execption [ID_NOT_EXISTS] Caso não encontre o registro do ID informado.
     */
    function findAutoNum($id){
        $id     = (int)$id;
        $out    = FALSE;
        if ($id > 0) {            
            $args['SQL']            = "SELECT * FROM ".$this->tableName." WHERE ".$this->colAutoNum."=%i";
            $args['WHERE_VALUES']   = $id;
            $row                    = $this->exec('FIRST_ROW',$args);
            if (is_array($row) && count($row) > 0) {
                $this->row = $this->arrParams = $out = $row;
            } else {
                //Registro não localizado       
                $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ID_NOT_EXISTS'); 
                $msgErr = str_replace('{ID}',$id,$msgErr);
                throw new \Exception( $msgErr );                
            }
        } else {             
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ID_ZERO');            
            throw new \Exception( $msgErr );
        }            
        return $out;
    }
    
    /**
     * A partir do nome da classe atual monta o nome da tabela (entidade do DB) a que faz referência.
     * 
     * Por exemplo, a classe Cliente.php representa a tabela SPRO_CLIENTE, CreditoConsolidado 
     * representa a tabela SPRO_CREDITO_CONSOLIDADO.
     * 
     * Código de exemplo:
     * 
     * <code>        
     *  $objCreditoConsolidado = new CreditoConsolidado();
     *  
     * //A linha abaixo imprime SPRO_CREDITO_CONSOLIDADO:
     *  echo $objCreditoConsolidado->getTable();
     * </code>
     * 
     * @return string Retorna o nome da tabela com a mesma grafia do DB.
     */    
    function getTable(){
        $classFullName  = get_class($this);//Nome com namespace. Ex: app\models\db\Cliente
        $arrClassParts  = explode('\\',$classFullName);//Separa as partes do namespace
        $className      = $tableName = $arrClassParts[count($arrClassParts)-1];//Guarda a última posição do array (nome da Classe)
        $strTableName   = preg_replace('/([a-z0-9])(?=[A-Z])/','$1 ',$className);        
        $arrTableName   = explode(' ',$strTableName);        
        if (is_array($arrTableName)) $tableName = implode("_",$arrTableName);//Nome composto. Concatena as partes do nome com "_"                      
        $this->tableName = $this->prefixoTable.strtoupper($tableName); //Concatena com o prefixo e converte para caixa alta        
        return $this->tableName;
    }
    
    /**
     * Faz a exclusão do registro atual.  
     * 
     * Caso o objeto contenha uma coleção de registros, é possível filtrar 
     * a coleção para excluí-los de uma só vez (exclusão em lote).
     * 
     * Exemplo 1 - Localizar/excluir um registro pelo seu ID:
     * <code>     
     *  $objCliente = new Cliente();
     *  $objCliente->findAutoNum(10);       
     *  if ($objCliente->delete() !== FALSE){
     *       echo 'Registro excluído com sucesso.';
     *  } else {
     *      echo 'Impossível excluir. Registro não encontrado.';
     *  }                 
     * </code>
     * 
     * Exemplo 2 - Localizar/excluir um grupo de registros:
     * <code>
     * $objCliente  = new Cliente();
     * $arrWhere    = array("PF_PJ=%s AND NEWSLETTER=%i",'PF',1);                                       
     * $numDel      = $objCliente->delete($arrWhere);      
     * if ($numDel !== FALSE) {
     *      echo $numDel. ' registro(s) excluído(s) com sucesso.';
     * } else {
     *      echo 'Nenhum registro foi excluído.';
     * }
     * </code>
     * 
     * @param string[] $arrWhere Opcional. 
     * <p>Exemplo: $arrWhere = array('PF_PJ=%s AND NEWSLETTER=%i','PF',1).
     * Observe que o primeiro índice (índice zero) do array contém a string da cláusula WHERE com variáveis que indicam
     * o tipo de registro (%i = integer, %s = string, %d = decimal/double, %ss = usar com LIKE etc) e os demais
     * índices contém as respectivas variáveis na ordem em que foram citadas no índice zero.    
     * </p>
     * 
     * @return boolean | integer 
     * <p>Retorna FALSE caso o objeto seja nulo ou vazio. 
     * Pode ocorrer após usar o método findAutoNum() e não localizar nenhum registro.
     * Ou então, retorna o total de registros excluídos.</p>
     */    
    function delete($arrWhere=NULL){
        if (is_null($arrWhere)) {            
            $pk = $this->colAutoNum;
            $id = (int)$this->$pk;
            if (strlen($pk) > 0 && $id > 0){                
                $arrWhere = array("$pk=%i",$id);
            } else {
                //O registro atual está vazio e nenhuma cláusula WHERE foi informada
                return FALSE;              
            }
        }
        return $this->exec('DELETE',$arrWhere);         
    }
    
    /**
     * Método auxliar de save(). 
     * Executa a operação de INSERT/UPDATE com os dados do objeto atual.
     * 
     * Caso já exista um registro com a mesma chave primária faz UPDATE.
     * Caso contrário faz INSERT.          
     * 
     * @return integer Retorna o ID (autoNumber) do registro cadastrado/alterado, ou então
     * retorna zero caso nenhuma modificação tenha ocorrido.
     * @throws \Exception [PARAMS_NULL] Caso o objeto atual não tenha parâmetros/atributos definidos.
     */
    private function insertUpdate(){
        $id         = 0;
        $arrParams  = $this->arrParams;
        if (is_array($arrParams)){
            $id = $this->exec('INSERT_UPDATE');
        } else {
            //O objeto atual está vazio (não contém dados de um registro).
            $msgErr = Dic::loadMsg(__CLASS__,NULL,__NAMESPACE__,'PARAMS_NULL');             
            throw new \Exception( $msgErr );            
        }
        return $id;
    }
    
    /**
     * Método auxliar de save(). 
     * Faz o INSERT do registro atual.          
     * 
     * 
     * @return integer Retorna o ID (autoNumber) do novo registro cadastrado.
     * @throws \Exception [PARAMS_NULL] Caso o objeto atual esteja vazio (sem dados de um registro válido). 
     * @throws \Exception [UNIQUE_FIELD_NULL] Caso um campo único obrigatório não tenha sido definido.
     * @throws \Exception [PK_CONFLICT] Caso exista conflito com um campo único.
     */
    private function insert(){
        $msgErr     = '';
        $table      = $this->getTable();
        $arrParams  = $this->arrParams;
        if (is_array($arrParams)) {
            //Verifica se há conflito com a chave primária
            $args       = array();//Variável que guarda um array de atributos a ser passados para DB::query
            $arrWhere   = NULL;
            $arrUnique  = $this->arrUnique;
            if (is_array($arrUnique)){                
                foreach($arrUnique as $val){
                    //echo $val.'<br>';                    
                    if (!isset($arrParams[$val])){
                        //Um campo de chave única não foi informado no Insert. Dispara um erro.
                        $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'UNIQUE_FIELD_NULL'); 
                        $msgErr = str_replace('{FIELD}',$val,$msgErr);
                        throw new \Exception( $msgErr );
                    }
                    $arrWhere[$val] = $arrParams[$val];//Cria um array associativo
                    //$args[]         = $arrParams[$val];
                }
                //print_r($arrUnique);
                //die();
                if (is_array($args) && is_array($arrWhere)){
                    $where          = $this->filterByOr($arrWhere);                    
                    $args['SQL']    = "SELECT ".$this->colAutoNum." FROM ".$table." WHERE ".$where;
                    $results        = $this->exec('ONE_FIELD',$args);
                    if (is_array($results) && count($results) >= 1) {
                         //Existe um registro em conflito (chave primária) com os dados informados.
                         $val       = join(', ',$arrUnique);
                         $msgErr    = Dic::loadMsg(__CLASS__,NULL,__NAMESPACE__,'PK_CONFLICT');
                         $msgErr    = str_replace('{TABLE}',$table,$msgErr);
                         $msgErr    = str_replace('{CONFLICT}',$val,$msgErr);
                    }
                }                
            }
            $id = (strlen($msgErr) == 0)?$this->exec('INSERT'):0;//Faz o INSERT apenas se não houver conflito.
        } else {
           $msgErr = Dic::loadMsg(__CLASS__,NULL,__NAMESPACE__,'PARAMS_NULL');               
        }
        if (strlen($msgErr) > 0) throw new \Exception( $msgErr );
        return $id;
    }
    
    /**
     * Faz o UPDATE do registro atual a partir do ID (autoNumber) informado na chamada do método.
     * 
     * @param integer $id Obrigatório. ID do registro a ser alterado.
     * @return integer Retorna 1 se alguma alteração ocorreu, ou zero caso nenhuma alteração tenha ocorrido.
     * @throws \Exception [PARAMS_NULL] Caso o parâmetro $id seja > 0 e o objeto atual esteja vazio (sem dados de um registro válido).
     * @throws \Exception [ID_ZERO] Caso o parâmetro $id seja = 0 ou inválido. 
     */
    function updateForId($id=0){
        $msgErr = '';
        if ((int)$id > 0){
            $pk         = $this->colAutoNum;
            $arrParams  = $this->arrParams;
            if (is_array($arrParams)) {
                $arrWhere = array($pk."=%i",$id);                
                return $this->exec('UPDATE',$arrWhere);
            } else {
                $msgErr = Dic::loadMsg(__CLASS__,NULL,__NAMESPACE__,'PARAMS_NULL');                            
            }
        } else {
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ID_ZERO');         
        }
        if (strlen($msgErr) > 0) throw new \Exception( $msgErr );
    }
    
    /**
     * Faz o UPDATE de um registro ou de um grupo de registros.
     * 
     * Exemplo1:<br>
     * Mostra a atualização do campo CIDADE para todos os registros 'PF'.
     * <code>     
     * $objCliente  = new Cliente(); 
     * $obj->CIDADE = 'São Paulo';
     * $arrWhere    = array('PF_PJ=%s','PF');
     * $id          = $obj->update($arrWhere); 
     * 
     * if ($id > 0) die("Gravou/atualizou o registro {$id}.");
     * echo 'Nada foi feito.';
     * </code>
     * 
     * Exemplo 2:<br>
     * Mostra a atualização de um único registro.
     * <code>
     * $objCliente  = new Cliente(); 
     * </code>
     * 
     * @param string[] $arrWhere Opcional. <br>
     * Exemplo: $arrWhere = array('PF_PJ=%s AND NEWSLETTER=%i','PF',1).
     * Observe que o primeiro índice (índice zero) do array contém a string da cláusula WHERE com variáveis que indicam
     * o tipo de registro (%i = integer, %s = string, %d = decimal/double, %ss = usar com LIKE etc) e os demais
     * índices contém as respectivas variáveis na ordem em que foram citadas no índice zero.  
     * 
     * @return $integer Retorna o total de linhas afetadas.
     * @throws \Exception [WHERE_NULL] Caso o parâmetro $arrWhere seja NULL ou inválido. 
     */    
    function update($arrWhere=NULL){
        $msgErr = '';
        if ($arrWhere != NULL){
            return $this->exec('UPDATE',$arrWhere);           
        } else {
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'WHERE_NULL');   
        }
        if (strlen($msgErr) > 0) throw new \Exception( $msgErr );
    }
    
    /**
     * Monta uma string WHERE com o operador AND.
     * 
     * Recebe um array associativo no formato $arr['NOME_DO_CAMPO'] = VALOR e 
     * retorna uma string WHERE com o operador AND.
     * 
     * Exemplo:
     * <code>      
     *  $arrFilterBy['NOME']    = 'João';
     *  $arrFilterBy['EMAIL']   = 'joao@server.com';
     *  $arrFilterBy['ID_UF']   = '1';
     * 
     * $obj                     = new Cliente();
     * $where                   = $obj->filterByAnd($arrFilterBy);
     *      
     * echo $where;
     * //Saída: NOME='João' AND EMAIL='joao@server.com AND ID_UF=1
     * </code>
     * 
     * @param string[] $arrFilterBy Obrigatório.
     * @return string
     */    
    function filterByAnd($arrFilterBy){
         return $this->filterBy($arrFilterBy,'and');
    }
    
    /**
     * Monta uma string WHERE com o operador OR.
     * 
     * Recebe um array associativo no formato $arr['NOME_DO_CAMPO'] = VALOR e 
     * retorna uma string WHERE com o operador OR.
     * 
     * @param string[] $arrFilterBy Obrigatório.
     * @return string
     */    
    function filterByOr($arrFilterBy){
        return $this->filterBy($arrFilterBy,'or');
    }
    
    /** 
     * Recebe um array associativo no formato $arr['COL_NAME']=VALUE e retorna 
     * uma string para ser usada na cláusula WHERE de acordo com o operador AND/OR
     * informado (parâmetro $andOr).
     *
     * A partir do índice associativo do array $arrFilterBy o tipo de cada campo é confrontado
     * com o tipo definido no DB para formar uma string com o devido tratamento ({@see setWhere()}).
     * Exemplo: 
     * Para o campo NOME do tipo string o $param informado para o objeto $where ficará NOME=%s.
     * Para o campo IDADE do tipo int o $param informado para o objeto $where ficará IDADE=%i.
     * 
     * @param string[] $arrFilterBy
     * @param string $andOr Define o tipo de filtro solicitado. Valores possíveis: AND|OR
     * @return string Retorna a string pronta que será concatenada com o WHERE. 
     * @throws \Exception [PARAMS_NULL] Caso um dos parâmetros ($arrFilterBy ou $andOr) seja NULL ou inválido. 
     * @throws \Exception [ERR_AND_OR] Caso o parâmetro $andOr possua valor diferente de 'AND' ou 'OR'. 
     */    
    private function filterBy($arrFilterBy,$andOr='and'){
        $where      = NULL;
        $sqlWhere   = NULL;
        $msgErr     = '';
        if (is_array($arrFilterBy) && is_string($andOr)){            
            $andOr = strtoupper($andOr);
            if ($andOr != 'AND' && $andOr != 'OR') {
                //Dispara uma exceção
                $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_AND_OR');   
            }
            $where = new \WhereClause($andOr); 
            foreach($arrFilterBy as $var=>$value){
                $param = $this->setWhere($var);//Retorna o formato de acordo com o tipo (Ex: NOME=%s )
                $where->add($param, $value);//concatena a string $param com a cláusula WHERE.
            }
        } else {
            //Dispara uma exceção
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'PARAMS_NULL');   
        }
        if (strlen($msgErr) > 0) throw new \Exception( $msgErr );
        $this->where = $sqlWhere = $where->text();//Retorna o resultado da string WHERE pronta.
        return $sqlWhere;        
    }
    
    /**
     * Gera uma lista das tabelas do DB atual. 
     * 
     * return string[] Retorna um array unidimensional com as tabelas do DB atual.
     */
    private function loadTables(){
        $arrTables  = \DB::tableList();
        //foreach ($arrTables as $table) echo "Table Name: $table\n";
        return $arrTables;
    }
    
    /**
     * Faz o INSERT (novo registro) ou UPDATE (caso encontre conflito com campo único) do registro atual.
     * 
     * IMPORTANTE: este método deve ser usado apenas para autalização/gravação de um único registro.
     * Para fazer atualizações em lote utilize o método <b>update()</b> com parâmetro WHERE.<br/>
     *       
     * Exemplo 1 - alterar o campo PF_PJ de um registro cujo ID (autoNumber) é igual a 10:
     * <code>
     * $obj = new Cliente(10);//Localiza o registro cujo ID = 10
     * $obj->PF_PJ = 'PJ';//Altera este campo para PJ
     * $id = $obj->save();
     * 
     * if ($id > 0){
     *   echo 'Alteração realizada com sucesso.';
     * } else {
     *   echo 'Nada foi alterado.';
     * }
     * </code>
     * 
     * Exemplo 2 - Novo registro para inclusão:
     * <code>
     * $obj = new Cliente();
     * $obj->LOGIN             = 'admin';
     * $obj->NOME_PRINCIPAL    = 'João Silva';     
     * $obj->EMAIL             = "joao@server.com";
     * $obj->PF_PJ             = 'PF';
     * $id                     = $obj->save();
     *      
     * if ($id > 0){
     *   echo 'Inclusão/alteraçaõ realizada com sucesso.';
     * } else {
     *   echo 'Nada foi feito.';
     * }      
     * </code>
     * 
     * @return integer <br>
     * Retorna o ID do registro gravado/alterado. Para a ação de UPDATE, se não houver alteração retorna zero. 
     */    
    function save(){
        //Verifica se o campo de chave primária foi fornecido.
        $arrParams    = $this->arrParams;  
        $results      = self::$results;
        if (is_array($results) && count($results) > 0){
            //O objeto atual possui vários registros. Ação não permitida.         
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_UPD_EM_LOTE');   
            throw new \Exception( $msgErr );            
        } else {
            if (!is_array($arrParams) || count($arrParams) == 0) return FALSE;
            //INSERT/UPDATE  
            return $this->insertUpdate();
        }
    }
    
    /**
     * Recebe o nome de uma coluna da tabela e gera uma string no formato COL_NAME=%type, 
     * onde type pode ser %i,%s,%d, %ls, %li, %ld, necessária para fazer consultas no mySql.
     * 
     * O tipo da coluna é confrontado com o formato definido na tabela do DB ($arrCols).
     * Exemplo: para o formato string o tipo será '%s' e para o formato integer o tipo será '%i'.
     * 
     * Para conhecer melhor os tipos permitidos, consulte o site {@link http://meekrodb.com}.
     *           * 
     * @param string $var Obrigatório. Nome da coluna (deve existir no DB)
     * @return string Retorna a string com o nome da coluna + código Sql. Exemplo: NOME=%s
     * @throws \Exception [COL_NAME_NOT_EXISTS] Caso os campos da tabela atual não tenham sido carregados corretamente.
     */    
    private function setWhere($var){
        $arrCols = $this->arrCols;//campos da tabela atual.
        if (!isset($arrCols[$var])) {
            //Não foi possível identificar campos para a tabela atual.
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'COL_NAME_NOT_EXISTS');   
            $msgErr = str_replace('{COL}',$var,$msgErr);
            throw new \Exception( $msgErr );
        }
        $colAtrib   = $arrCols[$var];//string de atributos da coluna atual separados por /.
        $param      = '';
        if (strlen($colAtrib) > 0) {
            //Encontrou a coluna no DB.            
           list($col,$typeLength,$null,$extra) = explode('/',$colAtrib);
           $arrType = explode('(',$typeLength);
           $type    = $arrType[0];//char | enum | int | date | datetime
            //Trata o tipo do dado, se necessário.
            switch($type){
                case 'int':
                    $param = $var.'=%i';
                    break;              
                case 'float':
                case 'double':
                    $param = $var.'=%d';
                    break;
                default:
                    $param = $var.'=%s';                   
            }           
        }
        return $param;
    }
    
    /**
     * Faz um JOIN padrão entre duas tabelas.
     * Exemplo - método criado dentro da classe Cliente:
     * <code>
     * class Cliente {
     *      ...
     *      
     *      function joinUf(){
     *          //Objeto referente à tabela A:
     *          $objA               = $this;
     *          $objA->alias        = 'a';    
     *          $objA->fieldsJoin   = 'NOME_PRINCIPAL,LOGIN,EMAIL';//Campos da tabela Cliente
     * 
     *          //Objeto referente à tabela B:
     *          $objB               = new Uf();
     *          $objB->alias        = 'b';    
     *          $objA->fieldsJoin   = 'UF';//Campos da tabela Uf
     * 
     *          //Array com campo(s) usado(s) para fazer o JOIN:
     *          $arrFields      = 'ID_UF';
     *          $a = $this->joinFrom($objA,$objB,$arrFields,'INNER');
     *          
     *          //Método alternativo:
     *          //$a = $this->innerJoinFrom($objA,$objB,$arrFields);
     * 
     *          //Finalização do JOIN passando um parâmetro WHERE (opcional):
     *          $this->setJoin("b.UF='SP'");
     * 
     *          //Executará a string SQL abaixo:
     *          //SELECT a.*,b.* FROM SPRO_CLIENTE a INNER JOIN SPRO_UF b ON a.ID_UF = b.ID_UF AND b.UF='SP' 
     *      }
     * }
     * 
     * //Chamada do método que faz o JOIN entre Cliente e Uf:
     *  $objCliente = new Cliente();
     *  $results    = $objCliente->joinUf();//Retorna um array de objetos da classe Cliente.
     * 
     * //Ou então...
     * $objCliente  = new Cliente();
     * $objCliente->joinUf();
     * $results     = $objCliente->load();//Retorna o array de objetos gerado ao chamar o método joinUf();
     * </code>
     * 
     * @param object $objTableA Obrigatório. Objeto referente à tabela A
     * @param object $objTableB Obrigatório. Objeto referente à tabela B
     * @param string $fieldMap Obrigatório. Campo usado para fazer o JOIN entre as duas tabelas.
     * @param string $type Opcional. Se não informado o padrão é 'INNER'. Valores possíveis:INNER | OUTER | LEFT | RIGHT.   
     * @return void
     * @throws \Exception [ERR_MAP_FIELDS] Caso o parâmetro $fieldMap não está no formato correto. 
     * @throws \Exception [ERR_JOIN_PARAM] Caso um ou mais parâmetros obrigatórios sejam inválidos ou incorretos.
     */
    protected function joinFrom($objTableA,$objTableB,$fieldMap,$type='INNER'){
        if (is_object($objTableA) && is_object($objTableB) && (is_array($fieldMap) || strlen($fieldMap) > 0)){
            $tableA             = $objTableA->getTable();
            $tableB             = $objTableB->getTable();
            $aliasA             = $objTableA->getAlias();
            $aliasB             = $objTableB->getAlias();
            $this->arrObjJoin   = array();
            $this->arrObjJoin[] = $objTableA;
            $this->arrObjJoin[] = $objTableB;    
            $fieldsMap          = "{$aliasA}.{$fieldMap} = {$aliasB}.{$fieldMap}";
            if (is_array($fieldMap)){
                //O JOIN deve ser feito entre dois campos ou mais (Ex: a.FIELD1 = b.FIELD1 AND a.FIELD2 = b.FIELD2...)
                $arrFieldsMap = array(); //Guarda o mapeamento dos campos entre as duas tabelas.
                foreach($fieldMap as $map){
                    //Cada item do array deve estar no formato campoA=campoB
                    $map    = str_replace(' ','',$map);//Retira espaço em branco
                    $arrMap = explode('=',$map);
                    if (count($arrMap) != 2){
                        //O mapeamento do índice atual não está no formato correto.
                        $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_MAP_FIELDS');   
                        throw new \Exception( $msgErr );                          
                    }
                    $fieldMapItem   = $aliasA.'.'.$map;
                    $fieldMapItem   = str_replace('=','='.$aliasB.'.',$fieldMapItem);
                    $arrFieldsMap[] = $fieldMapItem;
                }
                $fieldsMap = join($arrFieldsMap,' AND ');
            }
            $this->joinWhere    = "({$tableA} AS {$aliasA} {$type} JOIN {$tableB} AS {$aliasB} ON {$fieldsMap})";              
        } else {
            //Um ou mais parâmetros obrigatórios inválidos ou não informados.
            $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_JOIN_PARAM');   
            throw new \Exception( $msgErr );            
        }
    }
    
    /**
     * Alternativa para chamar o método joinFrom() para criar 'INNER JOIN'.
     * 
     * @param object $objTableA. Obrigatório.
     * @param object $objTableB. Obrigatório.
     * @param string $fieldMap. Obrigatório. 
     */
    protected function innerJoinFrom($objTableA,$objTableB,$fieldMap){
       $this->joinFrom($objTableA,$objTableB,$fieldMap,'INNER'); 
    }
    
    /**
     * Alternativa para chamar o método joinFrom() para criar 'OUTER JOIN'.
     * 
     * @param object $objTableA. Obrigatório.
     * @param object $objTableB. Obrigatório.
     * @param string $fieldMap. Obrigatório. 
     */    
    protected function outerJoinFrom($objTableA,$objTableB,$fieldMap){
        $this->joinFrom($objTableA,$objTableB,$fieldMap,'OUTER'); 
    }
    
    /**
     * Faz agrupamento de JOIN para a criação de SELECT com a junção de três ou mais tabelas.
     * 
     * Exemplo de Join em um método da tabela Cliente:
     * <code>
     * //Tabela 1:
     * $objA                = $this;//SPRO_CLIENTE
     * $objA->alias         = 'a';    
     * $objA->fieldsJoin    = 'NOME_PRINCIPAL,LOGIN,EMAIL';//Campos da tabela Cliente
     *           
     * //Tabela 2:
     * $objB                = new Uf();
     * $objB->fieldsJoin    = 'UF';
     * $objB->alias         = 'b';                
     *
     * //Tabela 3:
     * $objC                = new Profissao();
     * $objC->fieldsJoin    = 'PROFISSAO';
     * $objC->alias         = 'c';
     *  
     * $arrFields[] = 'ID_UF=ID_UF';//ID_UF da tabela A com ID_UF da tabela B
     * $arrFields[] = 'ID_CLIENTE=ID_LOGIN';//ID_CLIENTE da tabela A com ID_CLIENTE da tabela B
     *      
     * $this->joinFrom($objA,$objB,$arrFields);                
     * $this->joinFromAdd($objA,$objC,'ID_PROFISSAO');
     * $this->setOrderBy('UF ASC');
     * $this->setLimit(100);
     * $results = $this->setJoin("b.UF='SP'");        
     * </code>
     * 
     * @param object $objTableA
     * @param object $objTableB
     * @param string $fieldMap
     * @param string $type 
     */
    protected function joinFromAdd($objTableA,$objTableB,$field,$type='INNER'){
        $joinWhere = $this->joinWhere;
        if (strlen($joinWhere) > 0) {
            $tableA             = $objTableA->getTable();
            $aliasA             = $objTableA->getAlias();
            $aliasB             = $objTableB->getAlias();    
            $this->arrObjJoin[] = $objTableA;
            $this->arrObjJoin[] = $objTableB;
            $this->joinWhere    = "({$joinWhere} {$type} JOIN {$tableA} AS {$aliasA} ON {$aliasA}.{$field} = {$aliasB}.{$field})";            
        }       
    }
    
    protected function setJoin($where=''){
        $joinWhere      = $this->joinWhere;//String formada no método joinFrom()
        $arrObjJoin     = $this->arrObjJoin;
        $arrStrFields   = array();
        $fields         = '*';
        
        foreach($arrObjJoin as $obj){
            $alias      = $obj->getAlias();
            $fieldsJoin = $obj->fieldsJoin;
            if (strlen($fieldsJoin) == 0) $fieldsJoin = '*';
            
            //Coloca o alias nos campos informados em $fieldsJoin.
            $strFields      = $alias.'.'.$fieldsJoin;
            $strFields      = str_replace(',',','.$alias.'.',$strFields);
            $arrStrFields[] = $strFields;
        }
        
        if (count($arrStrFields) > 0) {
            $arrStrFields   = array_unique($arrStrFields);
            $fields         = join(',',$arrStrFields);                
        }
        
        $sql       = "SELECT $fields FROM ".$joinWhere;
        if (strlen($where) > 0) $sql .= " WHERE 1=1 AND {$where}";
        $sql       = $this->concatOrderByLimit($sql); 
        return self::query($sql);//Cada registro é um objeto stdClass         
    }

    /** 
     * Método auxiliar de toJson(), toArray() e toString.
     * Converte os dados do objeto atual no formato solicitado (ARRAY | JSON | STRING).
     * 
     * @param string $format Valores possíveis: ARRAY | JSON | STRING.
     * @return false | mixed[] | string
     */
    private function setFormatRow($format='ARRAY'){
        $row = $this->row;
        if (is_array($row)) {
            switch($format){
                case 'JSON':
                    $out = json_encode($row);
                    break;
                case 'STRING':
                    $out = join(',',$row);
                    break;
                default:
                    //ARRAY
                    $out = $row;
            }
            return $out;                          
        }
        return false;        
    }
    
    /**
     * Retorna os dados do objeto atual no formato JSON.
     * @return string 
     */
    function toJson(){
        return $this->setFormatRow('JSON');
    } 
    
    /**
     * Retorna os dados do objeto atual no formato ARRAY
     * @return mixed[]
     */
    function toArray(){
        return $this->setFormatRow('ARRAY');
    } 
    
    /**
     * Retorna os dados do objeto atual no formato STRING.
     * @return string
     */    
    function toString(){
        return $this->setFormatRow('STRING');
    }
    
    /**
     * @ignore     
     */    
    function __set($var,$value){
        $arrCols    = $this->arrCols;
        if (!isset($arrCols[$var])) {
            //A variável solicitada não existe.
            $msgErr = Dic::loadMsg(__CLASS__,NULL,__NAMESPACE__,'VAR_NOT_EXISTS');   
            $msgErr = str_replace('{VAR_NAME}',$var,$msgErr);
            $msgErr = str_replace('{TABLE}',  get_class($this),$msgErr);
            throw new \Exception( $msgErr );                         
        }
        
        $colAtrib   = $arrCols[$var];//Atributos da coluna atual.
        if (strlen($colAtrib) > 0) {
            //Encontrou a coluna no DB cujo valor foi definido.
            list($col,$typeLength,$null,$extra) = explode('/',$colAtrib);
            $arrType    = explode('(',$typeLength);
            $type       = $arrType[0];//char | enum | int | date | datetime
            //Trata o tipo do dado, se necessário.
            switch($type){
                case 'int':
                    $value = (int)$value;
                    break;
                default:
            }
            $this->id               = ($extra == 'auto_increment')?$value:0;
            $this->arrParams[$var]  = $value;
        }        
    } 
    
    /**
     * @ignore     
     */    
    function __get($var){
        $row = $this->row;
        if (is_array($row) && isset($row[$var])) return $row[$var];                         
        return false;
    }    
}
?>
