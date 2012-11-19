<?php
    
    namespace sys\classes\util;  
    
    class Cache {
            private $memcache;
            private $version;
            private $timeSec;
            
            function __construct(){
                    $memcache = new \Memcache();
                    if (!$memcache->connect('localhost', 11211)){
                        $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_MEMCACHE_CONN'); 
                        throw new \Exception( $msgErr );                         
                    }

                    $this->version  = $memcache->getVersion();                   								
                    $this->memcache = $memcache;
            }      
            
            function getVersion(){
                return $this->version;
            }

            /**
             * Guarda uma variável em cache.
             * 
             * @param string $name Nome da variável
             * @param string $content Conteúdo a ser guardado em cache (string ou objeto)             
             * @return boolean TRUE caso a variável seja armazenada com sucesso.
             * @throws \Exception Caso um erro ocorra ao guardar o contéudo informado em cache.
             */
            function setCache($name,$content){			
                if (!$this->memcache->set($name,$content, false, $this->timeSec)){
                    $msgErr = Dic::loadMsg(__CLASS__,__METHOD__,__NAMESPACE__,'ERR_SAVE_CACHE'); 
                    throw new \Exception( $msgErr );                                                   		
                }
                return TRUE;
            }

            /**
             * Exclui uma variável do cache.
             * 
             * @param string $name Nome da variável.
             * @return void
             */
            function delete($name){
                $this->memcache->delete($name);						
            }           

            /**
             * Define o tempo de vida do cache em dias.
             * 
             * @param integer $time Valor numérico que representa dias.
             * @return void
             */
            function setDay($time){
                 $this->setTime('DAY', $time);    
            }

            /**
             * Define o tempo de vida do cache em horas.
             * 
             * @param integer $time Valor numérico que representa horas.
             * @return void
             */            
            function setHour($time){
                $this->setTime('HOUR', $time);     
            }

            /**
             * Define o tempo de vida do cache em minutos.
             * 
             * @param integer $time Valor numérico que representa minutos.
             * @return void
             */            
            function setMin($time){
                $this->setTime('MIN', $time);                
            }

            /**
             * Define o tempo de vida do cache em segundos.
             * 
             * @param integer $time Valor numérico que representa segundos.
             * @return void
             */            
           function setSec($time){
               $this->setTime('SEC', $time);
            }
            
            /**
             * Método de suporte para setDay(), setHour(), setMin e setSec().
             * Define o tempo de vida do cache convertendo o valor informado em segundos.
             * 
             * @param string $period Pode ser DAY, HOUR, MIN ou SEC.
             * @param integer $time Tempo a ser convertido em segundos de acordo com o período informado em $period.
             * @return boolean TRUE caso um tempo válido tenha sido definido com sucesso, FALSE caso contrário.
             */
            function setTime($period,$time){
                $sec        = 0;
                $time       = (int)$time;
                $period     = strtoupper($period);
                $arrPeriod  = array('DAY','HOUR','MIN','SEC');
                $key        = array_search($period,$arrPeriod);
                $out        = FALSE;
                if ($key !== FALSE && $time > 0) {
                    switch ($period){
                        case 'DAY':
                            $sec = $time*24*60*60;
                            break;
                        case 'HOUR':
                            $sec = $time*60*60; 
                            break;
                        case 'MIN':
                            $sec = $time*60; 
                            break; 
                       default:
                            $sec = $time;                      
                    }
                    $this->timeSec = $sec;
                    $out = TRUE;
                }
                return $out;
            }

            /**
             * Limpa todo o conteúdo armazenado no Memcached.
             */
            function flush(){
                $this->memcache->flush();			
            }

            //RETORNA FALSE SE O CONTEÚDO NÃO ESTIVE EM CACHE, OU ENTÃO RETORNA O CONTEÚDO
            /**
             * Captura o valor de uma variável armazenada em cache.
             * 
             * @param string $name Nome da variável. 
             * @return mixed Retorna o valor da variável ou FALSE caso a variável não exista em cache.
             */
            function getCache($name){			
                $cache = $this->memcache->get($name);			
                return $cache;
            }		
    }
?>
