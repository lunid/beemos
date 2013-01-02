<?php

    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;
    use \app\classes\views\ViewSite;
    use \sys\classes\util\Request;
    
    class Planos extends Controller {
        /**
         * Conteúdo da página Planos
         */
        public function actionIndex(){
            try{                     
                $this->cacheOn(__METHOD__);
                
                //Home
                $objViewPart = new ViewPart('planos');
                
                //Template
                $tpl        = new ViewSite();
                
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'SuperPro Web | Planos';
                
                //Js para inclusão
                $tpl->setJs('app/planos');           
                
                $tpl->render('planos');            
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        public function actionIdentificacao(){
            try{                     
                $this->cacheOn(__METHOD__);
                
                //Recebe plano
                $idPlano = Request::get("plano", "NUMBER");
                
                if($idPlano <= 0){
                    echo "Selecione um plano para prosseguir";
                    die;
                }
                
                //Home
                $objViewPart = new ViewPart('planos_identifiquese');
                
                //Id do Plano para view
                $objViewPart->ID_PLANO = $idPlano;
                
                //Template
                $tpl        = new ViewSite();
                
                //Js para inclusão
                $tpl->setJs('app/planos');           
                
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'SuperPro Web | Identifique-se';

                $tpl->render('planos_identifiquese');            
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
