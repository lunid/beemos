<?php
    /**
    * Classe Controller usada com default quando nenhuma outra é informada.
    * Refere-se à página inicial do admin.
    */
    use \bm\classes\controllers\BmController;
    
    class Index extends BmController {

        /**
        *Conteúdo da página home do admin.
        */
        function actionIndex(){
            try{
                //Home
                $cache = TRUE;
                if ($cache) {
                    $this->cacheOn(__METHOD__);
                } else {
                    $this->cacheOff(__METHOD__);
                }
                
                $objViewPart = $this->mkViewPart('home');
                
                //Template
                $tpl = $this->mkView();                                
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'ADM';
                
                $tpl->setCss('bm.home');
                $tpl->forceCssJsMinifyOn();
                //$tpl->onlyExternalCssJs();
                
                $tpl->render('index',$this->getMemCache());            
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

