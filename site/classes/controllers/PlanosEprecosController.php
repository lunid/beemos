<?php

    use \sys\classes\mvc as mvc;   
    use \sys\classes\util\Request;
    use \common\classes\Menu;
    use \site\classes\models\PlanoModel;
    
    class PlanosEprecos extends mvc\ExceptionController {
        
        public function actionIndex(){
            $bodyHtmlName   = 'planosEprecos';
            $objViewPart    = mvc\MvcFactory::getViewPart($bodyHtmlName);                               
            $objView        = mvc\MvcFactory::getView();
            
            //Dados/Planos:
            $objPlanoModel  = new PlanoModel();
            $arrPlanoBasico = $objPlanoModel->getInfoPlano('BASICO');
            $arrPlanoProf   = $objPlanoModel->getInfoPlano('PROFISSIONAL');
            $arrPlanoCorp   = $objPlanoModel->getInfoPlano('CORPORATIVO');
            
            $objView->setLayout($objViewPart);
            $objView->TITLE         = 'Supervip - Planos & Preços';
            $objView->MENU_MAIN     = Menu::main(__CLASS__);
            $objView->RECURSOS      = $this->setHtmlPlano();
            $objView->INFO_PLANO_A  = $this->setHtmlPlano($arrPlanoBasico);
            $objView->INFO_PLANO_B  = $this->setHtmlPlano($arrPlanoProf);
            $objView->INFO_PLANO_C  = $this->setHtmlPlano($arrPlanoCorp);
            
            $listCss    = 'site.planosEprecos';
            $listJs     = 'site.planosEprecos';
            $listCssInc = '';
            $listJsInc  = '';
            $listPlugin = '';


            $objView->setCss($listCss);
            $objView->setJs($listJs);
            $objView->setPlugin('jquery_tools_tooltip');
            /*
            $objView->setJs($listJs);
            $objView->setCssInc($listCssInc);
            $objView->setJsInc($listJsInc);            
            */             
            $layoutName = 'planosEprecos';
            $objView->render($layoutName);            
            
        } 
        
        private function setHtmlPlano($arrInfoPlano=NULL){
            $htmlItens  = '';
            $i          = 0;
            
            $arrRecursos = array(
                'Custo de instalação:Taxa de configuração e instalação dos serviços contratados.',
                'Integração c/ cartões e bancos:Gateway de pagamento para integração rápida com Cielo, Redecard, e bancos (débito e boleto).',
                'Transações/mês:Número de transações/mês sem custo adicional.',
                'Custo por transação excedente:Valor por transação excedente no mês. O limite depende do seu plano (veja Transações/mês)',
                'Envio de faturas on-line:Recurso que permite o envio de faturas on-line para seus clientes.',
                'Agendamento de faturas:Permite organizar e agendar o envio de faturas on-line. Ideal para cobranças periódicas.',
                'Usuários:Número de usuários de sua equipe que podem usar os recursos com sua permissão'
             );
            
            //O índice de $arrCols deve coincidir com o do respectivo recurso em $arrRecursos.
            $arrCols = array(
                'VALOR_INSTALACAO:FLOAT',
                'GATEWAY:BIT',
                'NUM_TRANSACAO_MES:INT',
                'VALOR_TRANSACAO_EXCEDENTE:FLOAT',
                'ENVIO_FATURA_ONLINE:BIT',
                'AGENDAMENTO_ENVIO_FATURA:BIT',
                'NUM_USUARIOS:INT'                
            );
            
            if (is_array($arrInfoPlano)) { 
                $nomePlano      = strtoupper(trim($arrInfoPlano['PLANO']));
                $precoMensal    = $arrInfoPlano['VALOR_MES'];
                $htmlPreco      = $this->getHtmlPreco($precoMensal,'mensal');
                
                //Nome do Plano + Preço:
                $htmlItens      .= "<h5 class='preco-title'>".$nomePlano."</h5>{$htmlPreco}";
                $htmlItens      .= "<ul class='preco-feature'>";

                foreach($arrCols as $col){                    
                    list($field,$type) = explode(':',$col);
                    
                    $value  = $arrInfoPlano[$field];                    
                                        
                    if ($type == 'BIT') {
                        $simNao = ((int)$value == 1)?'icon_sim':'icon_nao';                       
                        $value  = "<div class='{$simNao}'></div>";
                    } elseif ($type == 'FLOAT') {
                        $value = number_format($value,2,',','.');
                    } elseif ($type == 'INT') {
                        $value = (int)$value;
                    }
                    
                    $rowGray = ($i%2 == 0)?'':"class='rowGray'";
                    $htmlItens .= "<li {$rowGray}>{$value}</li>";      
                    $i++;
                }
                $htmlItens .= "</ul>";
            } else {
                //Nenhum array com informações de plano foi informado.
                //Monta a coluna ref. aos recursos de cada plano.
                foreach($arrRecursos as $item){ 
                    list($label,$title) = explode(':',$item);
                    $rowGray    = ($i%2 == 0)?'':'rowGray';
                    $htmlItens .= "<li class='left {$rowGray}'>{$label}<span class='question_mark' title='{$title}'></span></li>";
                    $i++;
                }
            }
            return $htmlItens;
        }
        
        private function getHtmlPreco($valorDec,$prefixo){
            $valorDec = number_format($valorDec,2,'.','');
            list($inteiro, $decimal) = sscanf($valorDec, '%d.%d');
            if (strlen($decimal) == 0) $decimal = '00';
            $arrDec = 
            $htmlPreco = "
                <div class='preco-price'>
                    <h1>
                        <span class='moeda'>R$</span>
                        <span class='inteiro'>{$inteiro}</span>
                        <span class='decimal'>,{$decimal}</span>   
                        <div class='periodicidade'>{$prefixo}</div>
                    </h1>
                </div>";
            return $htmlPreco;
        }
        
        function actionCompare(){
            
        }
    }
?>
