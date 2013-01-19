<?php

    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;
    use \app\classes\views\ViewSite;
    use \sys\classes\util\Request;
    use \common\classes\models\UfModel;
    use \sys\classes\html\Combobox;
    use \sys\classes\util as Util;
    use \common\classes\models\UsuariosModel;
    use \sys\classes\security\Token;
    
    class Planos extends Controller {
        /**
         * Conteúdo da página Planos
         */
        public function actionIndex(){
            try{                     
                $this->cacheOn(__METHOD__);
                
                //Home
                $objViewPart = new ViewPart('planos');
                
                //Template
                $tpl        = new ViewSite();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'SuperPro Web | Planos';
                
                //Js para inclusão
                $tpl->setJs('app/planos');
                $tpl->forceCssJsMinifyOn();
                
                $tpl->render('planos');            
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
         * Carrega página com o segundo passo da compra. A identificação do usuário
         */
        public function actionIdentificacao(){
            try{                     
                $this->cacheOn(__METHOD__);
                
                //Recebe plano
                $idPlano = Request::get("plano", "NUMBER");
                
                if($idPlano <= 0){
                    echo "Selecione um plano para prosseguir";
                    die;
                }
                
                //Home
                $objViewPart = new ViewPart('planos_identifiquese');
                
                //Id do Plano para view
                $objViewPart->ID_PLANO = $idPlano;
                $objViewPart->CB_PF_UF = null;
                $objViewPart->CB_PJ_UF = null;
                
                //Combo de UF
                $mdUf = new UfModel();
                $rsUf = $mdUf->carregarUfCombo();
                
                if($rsUf->status){
                    //Propriedades para Obj HTML PF
                    $objParams              = new stdClass();
                    $objParams->cls         = "required";
                    $objParams->field_name  = "Estado";
                    $objParams->id          = "PF_UF";
                    $objParams->name        = "PF_UF";
                    
                    //HTML do Combo
                    $cbPfUf = new Combobox($objParams);
                    $cbPfUf->addOptions($rsUf->estados);
                    $objViewPart->CB_PF_UF = $cbPfUf->render();
                    
                    //Propriedades para Obj HTML PJ
                    $objParams->id      = "PJ_UF";
                    $objParams->name    = "PJ_UF";
                    
                    //HTML do Combo
                    $cbPfUf = new Combobox($objParams);
                    $cbPfUf->addOptions($rsUf->estados);
                    $objViewPart->CB_PJ_UF = $cbPfUf->render();
                }
                
                //Token
                $objTk              = new Token(2);
                $objViewPart->TOKEN = $objTk->protectForm();
                
                //Template
                $tpl        = new ViewSite();
                $tpl->setLayout($objViewPart);
                $tpl->TITLE = 'SuperPro Web | Identifique-se';
                
                //Js para inclusão
                $tpl->setJs('app/planos');  
                $tpl->forceCssJsMinifyOn();
                
                $tpl->render('planos_identifiquese');            
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }
        
        public function actionSalvarUsuario(){
            try{                     
                $this->cacheOn(__METHOD__);
                
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = "Falha ao salvar novo Usuário! Tente mais tarde.";
                
                if(Request::post("TIPO_PESSOA") == 'PF'){
                    //Verifica senha
                    if(Request::post("PF_PASSWD") != Request::post("PF_C_PASSWD")){
                        $ret->msg = "O campo Senha e Confirmar Senha são diferentes!";
                        echo json_encode($ret);
                        die;
                    }

                    //Recebe dados para serem salvos
                    $arrDados = array(
                        "ID_USER_PERFIL"    => 2, //Professor
                        "NOME"              => Request::post("PF_NOME"),
                        "APELIDO"           => Request::post("PF_APELIDO"),
                        "CPF_CNPJ"          => Util\Number::clearNumber(Request::post("PF_CPF")),
                        "DT_NASCIMENTO"     => Util\Date::formatDate(Request::post("PF_DT_NASCIMENTO"), "AAAA-MM-DD"),
                        "SEXO"              => Request::post("PF_SEXO"),
                        "EMAIL"             => strtolower(Request::post("PF_EMAIL")),
                        "DDD_TEL_RES"       => Util\Number::clearNumber(Request::post("PF_DDD_TEL_RES")),
                        "TEL_RES"           => Util\Number::clearNumber(Request::post("PF_TEL_RES")),
                        "CELULAR"           => Util\Number::clearNumber(Request::post("PF_DDD_CELULAR")) . Util\Number::clearNumber(Request::post("PF_CELULAR")),
                        "DDD_TEL_COM"       => Util\Number::clearNumber(Request::post("PF_DDD_TEL_COM")),
                        "TEL_COM"           => Util\Number::clearNumber(Request::post("PF_TEL_COM")),
                        "RAMAL_TEL_COM"     => Util\Number::clearNumber(Request::post("PF_RAMAL_TEL_COM")),
                        "PASSWD"            => md5(Request::post("PF_PASSWD")),
                        "CEP"               => Util\Number::clearNumber(Request::post("PF_CEP")),
                        "LOGRADOURO"        => Request::post("PF_LOGRADOURO"),
                        "NUMERO"            => Request::post("PF_NUMERO"),
                        "COMPLEMENTO"       => Request::post("PF_COMPLEMENTO"),
                        "BAIRRO"            => Request::post("PF_BAIRRO"),
                        "CIDADE"            => Request::post("PF_CIDADE"),
                        "UF"                => Request::post("PF_UF"),
                        "OBS"               => Request::post("PF_OBS"),
                    ); 
                    
                    //Completa cadastro
                    $arrDadosCadastro = array(
                        "PF_PJ"             => "PF",
                        "CPF_CNPJ"          => Util\Number::clearNumber(Request::post("PF_CPF")),
                        "SEXO"              => Request::post("PF_SEXO"),
                        "COD_POSTAL"        => Util\Number::clearNumber(Request::post("PF_CEP")),
                        "LOGRADOURO"        => Request::post("PF_LOGRADOURO"),
                        "NUMERO"            => Request::post("PF_NUMERO"),
                        "COMPLEMENTO"       => Request::post("PF_COMPLEMENTO"),
                        "BAIRRO"            => Request::post("PF_BAIRRO"),
                        "CIDADE"            => Request::post("PF_CIDADE"),
                        "UF"                => Request::post("PF_UF"),
                    );
                }else if(Request::post("TIPO_PESSOA") == 'PJ'){
                    //Verifica senha
                    if(Request::post("PJ_PASSWD") != Request::post("PJ_C_PASSWD")){
                        $ret->msg = "O campo Senha e Confirmar Senha são diferentes!";
                        echo json_encode($ret);
                        die;
                    }

                    //Recebe dados para serem salvos
                    $arrDados = array(
                        "ID_USER_PERFIL"    => 2, //Professor
                        "NOME"              => Request::post("PJ_NOME"),
                        "APELIDO"           => Request::post("PJ_APELIDO"),
                        "CPJ_CNPJ"          => Util\Number::clearNumber(Request::post("PJ_CPF")),
                        "DT_NASCIMENTO"     => Util\Date::formatDate(Request::post("PJ_DT_NASCIMENTO"), "AAAA-MM-DD"),
                        "SEXO"              => Request::post("PJ_SEXO"),
                        "EMAIL"             => strtolower(Request::post("PJ_EMAIL")),
                        "DDD_TEL_COM"       => Util\Number::clearNumber(Request::post("PJ_DDD_TEL_COM")),
                        "TEL_COM"           => Util\Number::clearNumber(Request::post("PJ_TEL_COM")),
                        "RAMAL_TEL_COM"     => Util\Number::clearNumber(Request::post("PJ_RAMAL_TEL_COM")),
                        "CELULAR"           => Util\Number::clearNumber(Request::post("PJ_DDD_CELULAR")) . Util\Number::clearNumber(Request::post("PJ_CELULAR")),
                        "PASSWD"            => md5(Request::post("PJ_PASSWD")),
                        "CEP"               => Util\Number::clearNumber(Request::post("PJ_CEP")),
                        "LOGRADOURO"        => Request::post("PJ_LOGRADOURO"),
                        "NUMERO"            => Request::post("PJ_NUMERO"),
                        "COMPLEMENTO"       => Request::post("PJ_COMPLEMENTO"),
                        "BAIRRO"            => Request::post("PJ_BAIRRO"),
                        "CIDADE"            => Request::post("PJ_CIDADE"),
                        "UF"                => Request::post("PJ_UF"),
                        "OBS"               => Request::post("PJ_OBS"),
                    ); 
                    
                    //Completa cadastro
                    $arrDadosCadastro = array(
                        "PF_PJ"             => "PJ",
                        "CPF_CNPJ"          => Util\Number::clearNumber(Request::post("PJ_CPF_CNPJ")),
                        "RAZAO_SOCIAL"      => Request::post("PJ_RAZAO_SOCIAL"),
                        "INSC_ESTADUAL"     => Util\Number::clearNumber(Request::post("PJ_INSC_ESTADUAL")),
                        "INSC_MUNICIPAL"    => Util\Number::clearNumber(Request::post("PJ_INSC_MUNICIPAL")),
                        "NOME_CONTATO"      => Request::post("PJ_NOME_CONTATO"),
                        "WEBSITE"           => strtolower(Request::post("PJ_WEBSITE")),
                        "COD_POSTAL"        => Util\Number::clearNumber(Request::post("PJ_CEP")),
                        "LOGRADOURO"        => Request::post("PJ_LOGRADOURO"),
                        "NUMERO"            => Request::post("PJ_NUMERO"),
                        "COMPLEMENTO"       => Request::post("PJ_COMPLEMENTO"),
                        "BAIRRO"            => Request::post("PJ_BAIRRO"),
                        "CIDADE"            => Request::post("PJ_CIDADE"),
                        "UF"                => Request::post("PJ_UF"),
                        "DDD_TEL_2"         => Util\Number::clearNumber(Request::post("PJ_DDD_TEL_2")),
                        "TEL_2"             => Util\Number::clearNumber(Request::post("PJ_TEL_2")),
                        "RAMAL_TEL_2"       => Util\Number::clearNumber(Request::post("PJ_RAMAL_TEL_2")),
                        "DDD_FAX"           => Util\Number::clearNumber(Request::post("PJ_DDD_FAX")),
                        "FAX"               => Util\Number::clearNumber(Request::post("PJ_TEL_2")),
                        "RAMAL_FAX"         => Util\Number::clearNumber(Request::post("PJ_RAMAL_FAX")),
                    );
                }
                
                //Instância o Model de Usuário e Salva usuário
                $mdUsuarios = new UsuariosModel();
                $ret        = $mdUsuarios->salvarUsuario($arrDados);
                
                //Verifica retorno 
                if(!$ret->status){
                    echo json_encode($ret);
                    die;
                }
                
                //Atribui ID criado para o cadastro
                $arrDadosCadastro["ID_USER"] = $ret->id;
                
                //Completa dados cadastrais
                $ret = $mdUsuarios->salvarUsuarioCadastro($arrDadosCadastro);
                
                echo json_encode($ret);
                die;
            }catch(Exception $e){
                //Objeto de retorno
                $ret            = new stdClass();
                $ret->status    = false;
                $ret->msg       = $e->getMessage();
                
                echo json_encode($ret);
                die;
            }
        }
    }

?>
