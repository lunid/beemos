<?php
    namespace admin\classes\models;
    use \sys\classes\util\Date;
    use \sys\classes\mvc\Model;    
    use \db_tables as TB;
    
    class CaixaPostalModel extends Model {
        /**
         * Carrega lista de alunos relacionados a um cliente e suas turmas e escolas
         * 
         * @param int $ID_CLIENTE
         * @param string $where WHERE para filtro de SQL
         * @param array $arrPg Array com dados de Ordenação e Paginação Ex:
         * <code>
         * array(
         *   "campoOrdenacao"    => 'NOME_PRINCIPAL', 
         *   "tipoOrdenacao"     => 'DESC', 
         *   "inicio"            => 1, 
         *   "limite"            => 10
         * )
         * </code>
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  array   $ret->alunos    - Armazena os resultados (se encontrados)           <br />
         * </code>
         * 
         * @throws Exception
         */
        public function carregarAlunosPara($ID_CLIENTE, $where = '', $arrPg = null){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao carregar lista de Alunos!";
                
                //Valida ID_CLIENTE
                if((int)$ID_CLIENTE <= 0){
                    $ret->msg = "ID_CLIENTE inválido ou nulo!";
                    return $ret;
                }
                
                //Instância da table SPRO_TURMA
                $tbTurma  = new TB\Turma;
                
                //Carrega alunos do cliente
                $ret      = $tbTurma->listarAlunosCliente($ID_CLIENTE, $where, $arrPg);
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
        
        /**
         * Salva a mensagem de envio no banco e dispara para cada destinatário via sistema e via e-mail
         * 
         * @param int $idCliente Código do cliente que está enviando a mensagem
         * @param string $para E-mail(s) do(s) destinatário(s)
         * @param string $assunto Texto de assunto
         * @param string $msg Texto com a mensagem
         * 
         * @return stdClass $ret
         * <code>
         *  <br />
         *  bool    $ret->status    - Retorna TRUE ou FALSE para o status do Método     <br />
         *  string  $ret->msg       - Armazena mensagem ao usuário                      <br />
         *  array   $ret->id        - Código da mendagem na table SPRO_CAIXA_MSG        <br />
         *  array   $ret->sms       - Array com IDs de cliente que possuem SMS          <br />
         * </code>
         * 
         * @throws Exception
         */
        public function salvarMensagem($idCliente, $para, $assunto, $msg){
            try{
                //Objeto de retorno
                $ret            = new \stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao disparar mensagem!";
                
                //Valida ID_CLIENTE
                if($idCliente <= 0){
                    $ret->msg = "ID_CLIENTE inválido ou nulo!";
                    return $ret;
                }
                
                //Array de e-mails
                $arrPara = explode(";", $para);
                
                //Valida destinatários
                if(sizeof($arrPara) <= 0){
                    $ret->msg = "Nenhum destinatário encontrado!";
                    return $ret;
                }
                
                //Salva registro na tabela
                $tbCaixa = new TB\CaixaMsg();

                $tbCaixa->ID_CLIENTE    = $idCliente;
                $tbCaixa->PARA          = trim($para);
                $tbCaixa->ASSUNTO       = trim($assunto);
                $tbCaixa->MSG           = trim($msg);
                $tbCaixa->TIPO          = 'envio';
                $tbCaixa->DT_ENVIO      = date("Y-m-d H:i:s");

                $idMsg = $tbCaixa->save();

                if($idMsg <= 0){
                    $ret->msg = "Falha ao salvar mensagem no banco! Tenta mais tarde.";
                    return $ret;
                }
                    
                //Verifica usuários
                $tbCliente  = new TB\Cliente();
                $emails     = ""; //Variável para concatenação de e-mails
                $sms        = array(); //Array de clientes com celular para disparo de SMS
                
                foreach($arrPara as $email){
                    //Tenta carregar o cliente atravé do e-mail
                    $rsCliente = $tbCliente->findAll("EMAIL = '" . trim($email) . "'");
                    
                    //Adiciona vírgula ao disparo
                    if($emails != ""){
                        $emails  .= ", ";
                    }
                        
                    //Verifica se o cliente foi carregado
                    if($rsCliente->count() > 0){
                        $tbCaixa = new TB\CaixaMsg();
                        $cliente = $rsCliente->getRs()[0]; //Obtem retorno
                        
                        $tbCaixa->ID_CLIENTE    = $cliente->ID_CLIENTE;
                        $tbCaixa->SPRO_MSG_ID   = $idMsg;
                        $tbCaixa->ASSUNTO       = trim($assunto);
                        $tbCaixa->PARA          = trim($email);
                        $tbCaixa->MSG           = trim($msg);
                        $tbCaixa->TIPO          = 'recebida';
                        $tbCaixa->STATUS        = 'nao_lida';
                        $tbCaixa->DT_ENVIO      = date("Y-m-d H:i:s");
                        
                        $tbCaixa->save();
                        
                        if($cliente->FONE_CELULAR != ''){
                            $sms[] = $cliente->ID_CLIENTE;
                        }
                        
                        $emails .= $email;
                    }else{
                        //Concatena e-mail para disparo simples
                        $emails .= $email;
                    }
                }
                
                //Dispara e-mail para todos destinatários
                //TODO trocar e-mail
                mail("mpcbarone@gmail.com", $assunto, $msg);
                
                //Atualiza status do registro
                $tbCaixa            = new TB\CaixaMsg($idMsg);
                $tbCaixa->STATUS    = 'enviada';
                $tbCaixa->update(array('ID_SPRO_CAIXA_MSG = %i', $idMsg));
                
                $ret->status    = true;
                $ret->msg       = "E-mail enviado com sucesso!";
                $ret->id        = $idMsg;
                $ret->sms       = sizeof($sms > 0) ? $sms : false;
                
                return $ret;
            }catch(Exception $e){
                throw $e;
            }
        }
    }
    
?>
