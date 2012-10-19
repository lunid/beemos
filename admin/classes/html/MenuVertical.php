<?php

     namespace sys\classes\html;
     
     class MenuVertical extends Html {            
         private $arrItens = array();
         
         function __construct(){
            //Informa o nome do arquivo phtml a ser usado na classe atual:
            $this->setHtml('itens');            
         }
         
         function addItem($link,$text){                          
             $this->arrItens[]  = array('LINK'=>$link,'TEXT'=>$text);             
             $this->itens       = $this->arrItens;
         }
         
     }
?>
