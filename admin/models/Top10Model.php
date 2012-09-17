<?php
    namespace admin\models;
    use \sys\classes\mvc\Model;        
    use \admin\models\tables\BcoQuestao;
    use \sys\db\ORM; 
    
    class Top10Model extends Model {
        public function listaQuestoesTop10Materia(){
            try{
                $questao = new BcoQuestao();
                
                $questao->setLimit(10);
                
                return $questao->findAll();
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal - Top10Model <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
    }
?>
