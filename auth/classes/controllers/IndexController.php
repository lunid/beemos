<?php
    
    use \auth\classes\models\AuthModel;
    use \sys\classes\util\Request;
    
    class Index {
        
        public function actionIndex(){
            try{
                
                $objAuthModel   = new AuthModel();
                
                $user   = Request::post('user');
                $passwd = Request::post('passwd');
                
                $ret = new \StdClass();
                $ret->rows[0]['id']     = 0;
                $ret->rows[0]['cell']   = array('teste');
                json_encode($ret);
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
    }
?>
