<?php
    
    namespace admin\classes\html;
    use \sys\classes\html\Html;      
    
    /**
     * Classe que monta o HTML de uma nova Aba de Lista na área de Admin > Minhas Listas
     */
    class AbaLista extends Html {
         private $attr;
         
         /**
          * Método construtor
          */
         public function __construct(){
            //Informa o nome do arquivo phtml a ser usado na classe atual:            
            $this->setHtml('abaLista');     
            
            //Define os parâmetros específicos da classe atual:            
            $this->addParam('attrs');
         }
         
         public function setAttr($attr, $value){
            try{
                $this->attr[$attr]  = $value;
                $this->attrs        = $this->attr;
            }catch(Exception $e){
                throw $e;
            }
         }
         
         public function getAttr($attr){
            try{
                return $this->attr[$attr];
            }catch(Exception $e){
                throw $e;
            }
         }
     }
?>
