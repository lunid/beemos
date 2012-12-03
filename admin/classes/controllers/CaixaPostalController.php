<?php
    use \admin\classes\controllers\AdminController;
    use \sys\classes\mvc\View;
    use \sys\classes\mvc\ViewPart;
    use \sys\classes\util\Date;
    use \sys\classes\util\Request;
    use \admin\classes\models\CaixaPostalModel;
    
    class CaixaPostal extends AdminController {
        /**
         * Inicializa a página de Caixa Postal
         */
        public function actionIndex(){
            try{
                //View do Grid de Escolas
                $objViewPart = $this->mkViewPart('admin/caixa_postal');
                
                //Template
                $tpl                = $this->mkView();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE         = 'ADM | SuperPro';
                $tpl->SUB_TITULO    = 'Caixa Postal';
                
                $tpl->setJs('admin/caixa_postal');
                //$tpl->setCss('admin/caixa_postal');
                
                $tpl->forceCssJsMinifyOn();
                
                $tpl->render('caixa_postal');
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        /**
         * Traz o array jSon para que seja possível montar o grid de alunos
         */
        public function actionListarAlunosPara(){
            try{
                //Obejto de retorno
                $ret = new stdClass();
                
                //Model de Caixa Postal
                $mdCaixaPostal = new CaixaPostalModel();
                
                //Verifica filtros
                $where  = "";
                $search = Request::get('_search'); 
                if($search == true){
                    //Código do aluno
                    $ID_CLIENTE = Request::get('ID_CLIENTE', 'NUMBER');
                    if($ID_CLIENTE > 0){
                        $where .= " AND C.ID_CLIENTE LIKE '%" . $ID_CLIENTE . "%'";  
                    }
                    
                    //Nome do aluno
                    $NOME_PRINCIPAL = Request::get('NOME_PRINCIPAL');
                    if($NOME_PRINCIPAL != ''){
                        $where .= " AND C.NOME_PRINCIPAL LIKE '%" . $NOME_PRINCIPAL . "%'";  
                    }
                    
                    //Nome da escola
                    $NOME = Request::get('ESCOLA');
                    if($NOME != ''){
                        $where .= " AND E.NOME LIKE '%" . $NOME . "%'";  
                    }
                    
                    //Nome da Turma
                    $CLASSE = Request::get('CLASSE');
                    if($CLASSE != ''){
                        $where .= " AND T.CLASSE LIKE '%" . $CLASSE . "%'";  
                    }
                }
                
                //Lista todas escolas encontradas
                $rs = $mdCaixaPostal->carregarAlunosPara(26436, $where);
                
                if($rs->status){
                    $page           = Request::get('page', 'NUMBER'); 
                    $limit          = Request::get('rows', 'NUMBER'); 
                    $orderField     = Request::get('sidx'); 
                    $orderType      = Request::get('sord'); 

                    if(!$orderField) $orderField = 1;

                    //Total de registros
                    $count          = sizeof($rs->alunos);
                    $total_pages    = $count > 0 ? ceil($count/$limit) : 0;
                    $page           = $page > $total_pages ? $total_pages : $page;
                    $start          = $limit * $page - $limit;
                    
                    //Efetua select com ordenação e paginação
                    $rs = $mdCaixaPostal->carregarAlunosPara(
                            26436, 
                            $where, 
                            array(
                                "campoOrdenacao"    => $orderField, 
                                "tipoOrdenacao"     => $orderType, 
                                "inicio"            => $start, 
                                "limite"            => $limit
                            )
                    );
                    
                    $ret->page      = $page;
                    $ret->total     = $total_pages;
                    $ret->records   = $count;

                    $i=0;
                    foreach($rs->alunos as $row) {
                        $ret->rows[$i]['id']   = $row['ID_CLIENTE'];
                        $ret->rows[$i]['cell'] = array(
                            "<input type='checkbox' id='aluno_{$row['ID_CLIENTE']}' name='aluno_{$row['ID_CLIENTE']}' class='check_aluno' value='{$row['EMAIL']}' />",
                            $row['ID_CLIENTE'],
                            $row['NOME_PRINCIPAL'],
                            $row['ESCOLA'],
                            $row['CLASSE']
                        );
                        $i++;
                    }
                }else{
                    $ret                    = new stdClass();
                    $ret->rows[0]['id']     = 0;
                    $ret->rows[0]['cell']   = array($rs->msg);
                }

                echo json_encode($ret);
            }catch(Exception $e){
                $ret                    = new stdClass();
                $ret->rows[0]['id']     = 0;
                $ret->rows[0]['cell']   = array('Erro: ' . $e->getMessage() . " <br /> Arquivo: " . $e->getFile() . " <br /> Linha: " . $e->getLine());
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Dispara mensagem salvando no banco e por e-mail
         */
        public function actionEnviarMensagem(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao disparar Mensagem! Tente mais tarde.";
                
                //Instancia oModel e efetua disparo
                $mdCaixaPostal  = new CaixaPostalModel();
                $ret            = $mdCaixaPostal->salvarMensagem(
                        26436, 
                        Request::post('escrever_para'), 
                        Request::post('escrever_assunto'), 
                        Request::post('escrever_msg')
                );
                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Erro: " . utf8_decode($e->getMessage()) . " - Arquivo: " . $e->getFile() . " - Linha: " . $e->getFile();
                
                echo json_encode($ret);
            }
        }
        
        /**
         * Dispara SMS da mensagem atual
         */
        public function actionEnviarSms(){
            try{
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Implementar ação SMS.";
                                
                echo json_encode($ret);
            }catch(Exception $e){
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Erro: " . utf8_decode($e->getMessage()) . " - Arquivo: " . $e->getFile() . " - Linha: " . $e->getFile();
                
                echo json_encode($ret);
            }
        }
    }
?>