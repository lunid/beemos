<?php
   
    use \admin\classes\controllers\AdminController;
    
    class Usuarios extends AdminController {

        /**
        *Conteúdo da página home do admin.
        */
        function actionIndex(){
            try{
                //Home
                $objViewPart = $this->mkViewPart('usuarios');
                
                //Template
                $tpl                = $this->mkView();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE         = 'ADM | SuperPro';
                $tpl->SUB_TITULO    = 'Usuários';
                
                $tpl->setPlugin('jqgrid');
                
                $tpl->forceCssJsMinifyOn();
                
                $tpl->render('usuarios');            
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        public function actionListaUsuarios(){
            try{
                $ret    = new \stdClass();
                
                $page   = Request::get('page'); // página requisitada
                $limit  = Request::get('rows'); // número de linhas do grid
                $sidx   = Request::get('sidx'); // índice da linha - ex.: usuário clicou para ordernar 
                $sord   = Request::get('sord'); // direção (ASC/DESC)
                if(!$sidx) $sidx = 1;
                
                $m_admusuario   = new AdmUsuario();
                $rs_total       = $m_admusuario->findAll();
                $count          = $rs_total->count();
                
                if($count <= 0){
                    $ret->rows[0]['id']     = 1;
                    $ret->rows[0]['cell']   = array(0,'Nenhum usuário encontrado',null,null,null);
                    echo json_encode($ret);
                    die;
                }
                
                $total_pages    = $count > 0 ? ceil($count/$limit) : 0;
                $page           = $page > $total_pages ? $total_pages : $page;
                $start          = $limit * $page - $limit;
                
                $m_admusuario->setOrderBy($sidx . " " . $sord);
                $m_admusuario->setLimit($limit, $start);
                
                $rs             = $m_admusuario->findAll();
                $ret->page      = $page;
                $ret->total     = $total_pages;
                $ret->records   = $count;
                
                $i = 0;
                foreach($rs as $row){
                    $ret->rows[$i]['id']    = $row->ID_USUARIO;
                    $ret->rows[$i]['cell']  = array($row->ID_USUARIO,$row->NOME,$row->EMAIL,$row->TELEFONE,Date::formatDate($row->DATA_REGISTRO));
                    $i++;
                }
                
                echo json_encode($ret);
                die;
            }catch(Exception $e){
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
                die;
            }
        }
    }
?>

