<?php
    namespace app\models;
    use \sys\classes\mvc\Model;        
    use \app\models\tables\Cliente;
    use \app\models\tables\MateriaQuestao;
    use \app\models\tables\IndicaAmigo;
    use \sys\db\ORM; 
    
    class HomeModel extends Model {
        
        function getTotalQuestoesDb(){
            return $this->getTotalQuestoes('DB');
        }
        
        function getTotalQuestoesEnem(){
            return $this->getTotalQuestoes('ENEM');
        }
        
        private function getTotalQuestoes($filtro){
            $obj = new MateriaQuestao();
            $total = ($filtro == 'ENEM')?$obj->getTotalQuestoesEnem():$obj->getTotalQuestoesDb();
            return $total;
        }        
    }
?>
