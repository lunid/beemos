<?php
    
    use \sys\classes\mvc\Controller;        
    use \sys\classes\mvc\ViewPart;      
    use \sys\classes\mvc\View;
    use \sys\classes\util\Request;
    
    class Index extends Controller {
        
        public function actionIndex(){
            try{
                
                /*
                 * Ativa/usa o ache para a página atual.
                 * Se houver um conteúdo em cache, a saída é mostrada (impressa) 
                 * e o script interrompido nesta linha.
                 * É necessário que o módulo de memcache esteja ativo.
                 */
                //$this->cacheOn(__METHOD__);
                
                /*
                 * Desativa e limpa o cache para a página atual, caso exista.
                 * É necessário que o módulo memcache esteja ativo.
                 */
                //$this->cacheOff(__METHOD__);
                
                /*
                 * View que carrega uma parte da interface em html
                 * O parâmetro de viewPart deve ser um arquivo html válido, 
                 * localizado em viewParts/br/.
                 * O conteúdo deste arquivo será concatenado ao template definido em view().
                 */                
                $objViewPart = new ViewPart('home');
                
                /*
                 * Define valor para os marcadores do html.
                 * Os marcadores são incluídos entre chaves, por exemplo {NOME},
                 * no arquivo definido em viewPart().
                 */
                $objViewPart->NOME  = 'Fulano';
                $objViewPart->HOJE  = date('d/m/Y H:i:s');
                $objViewPart->MENU  = date('d/m/Y H:i:s');
                
                /*
                 * Cria uma view que irá concatenar o contéudo de $objViewPart com
                 * um template. Caso nenhum template seja definido, o padrao.html será usado.
                 */
                $objView            = new View();
                $objView->setLayout($objViewPart);
                $objView->TITLE     = 'Modelo de exemplo MVC';
                
                
                /*
                 * Define os includes css e js específicos da página atual.
                 * Formato do parâmetro para css, cssInc, js e jsInc:
                 * nomeDoModulo/nomeDoArquivoCssOuJsSemExtensao
                 * 
                 * Para o parâmetro $listPlugin, informe apenas o nome da pasta onde 
                 * o plugin se encontra. Lembrando que todos os plugins devem ser armazenados em /assets/plugins/.                 
                 */
                
                /*
                $listCss    = '';
                $listJs     = '';
                $listCssInc = '';
                $listJsInc  = '';
                $listPlugin = '';
                                
                $objView->setCss($listCss);
                $objView->setJs($listJs);
                $objView->setCssInc($listCssInc);
                $objView->setJsInc($listJsInc);
                $objView->setPlugin($listPlugin);
                */
                
                /*
                 * Força a compactação de arquivos de inclusão, mesmo se o arquivo final (_min) já existir.
                 * Por padrão, se o ambiente atual estiver definido para 'dev' esta opção já estará ativa.
                 */
                //$tpl->forceCssJsMinifyOn();
                
                /*
                 * Força a inclusão individual de arquivos css e js.
                 * Isso é útil caso você queira conferir quais os arquivos estão sendo incluídos no arquivo '_min'.
                 */
                //$tpl->onlyExternalCssJs();
                
                /*
                 * Renderiza e mostra a saída HTML.
                 * O parâmettro $layoutName representa o nome do(s) arquivo(s) de destino para includes css e js.
                 * O conteúdo desse arquivo é resultado da concatenação de um ou mais arquivos de inclusão e, 
                 * por padrão, é criado em /assets/cssOujs/nomeDoModulo/_layoutName_min.cssOujs.
                 */                
                $layoutName = 'index';
                $objView->render($layoutName);
                
            }catch(Exception $e){
                echo ">>>>>>>>>>>>>>> Erro Fatal <<<<<<<<<<<<<<< <br />\n";
                echo "Erro: " . $e->getMessage() . "<br />\n";
                echo "Arquivo:  " . $e->getFile() . "<br />\n";
                echo "Linha:  " . $e->getLine() . "<br />\n";
                echo "<br />\n";
                die;
            }
        }                
    }
?>
