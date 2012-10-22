<?php
    
    namespace admin\classes\html;
    use \sys\classes\html\Html;      
    
     class MenuVertical extends Html {            
         private $arrItens   = array();
         private $itemSel    = '';
         
         function __construct(){
            //Informa o nome do arquivo phtml a ser usado na classe atual:            
            $this->setHtml('menuVertical');         

            //Define os parâmetros específicos da classe atual:
            $this->addParam('itens');         
         }
         
         function setItemSel($itemSel){
             $this->itemSel = $itemSel;
         }
         
         function addItem($cls,$link,$text){
             $sel               = ($this->itemSel == $cls)?1:0;
             $this->arrItens[]  = array('CLS'=>$cls,'LINK'=>$link,'TEXT'=>$text,'SELECTED'=>$sel);             
             $this->itens       = $this->arrItens;
         }
         
     }
?>
