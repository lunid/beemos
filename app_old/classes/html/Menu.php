<?php
    
    namespace app\classes\html;
    use \sys\classes\html\Html;      
    
     class Menu extends Html {            
         private $arrItens   = array();
         
         function __construct(){
            //Informa o nome do arquivo phtml a ser usado na classe atual:            
            $this->setHtml('menu');         

            //Define os parâmetros específicos da classe atual:            
            $this->addParam('itens');                  
            $this->addParam('itemSel');                  
//            
//            //Carrega os nós do menu a partir de um XML:
//            $objModule  = new \Module();
//            $xmlPath    = $objModule->viewPartsLangFile($xmlFile);            
//            if (file_exists($xmlPath)) {
//                $objXml = Xml::loadXml($xmlPath); 
//                if (is_object($objXml)){
//                    $lang        = \Application::getLanguage();
//                    $nodes       = $objXml->$lang->itemMenuParams;          
//                    $numItens    = count($nodes);
//                    
//                    if ($numItens > 0) {
//                        $arrNodes  = self::convertNode2Array($nodes);                        
//                        foreach($nodes as $node){
//                            $cls        = Xml::valueForAttrib($node,'id','cls');
//                            $controller = Xml::valueForAttrib($node,'id','controller');
//                            $text       = Xml::valueForAttrib($node,'id','text');
//                            $this->addItem($cls,$controller,$text);
//                        }
//                    }
//                }                        
//            }    
         }
         
         function setItemSel($itemSel){
             $this->itemSel = $itemSel;
         }
         
         function addItem($text, $href, $itemName){
             //$sel               = ($this->itemSel == $cls)?1:0;
             $this->arrItens[]  = array(
                                    'ITEM'  => $itemName,
                                    'TEXT'  => $text,
                                    'HREF'  => $href
                                  );             
             $this->itens       = $this->arrItens;
         }
         
     }
?>
