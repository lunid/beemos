<?php
    
    namespace app\classes\html;
    use \sys\classes\html\Html;      
    
     class MenuInterno extends Html {            
         private $arrItens   = array();
         
         function __construct(){
            //Informa o nome do arquivo phtml a ser usado na classe atual:            
            $this->setHtml('menuInterno');         

            //Define os parâmetros específicos da classe atual:            
            $this->addParam('itens');                  
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
