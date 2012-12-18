<?php
    session_start();
    error_reporting(-1);
    
    if (!ini_get('display_errors')) {
        ini_set('display_errors', '1');
    }
    
    ini_set('display_errors', true);     
          
    include('sys/classes/_init/Application.php');
     
    try {
        Application::folder('interbits');
        
        /*
         * Define o ambiente atual.
         * Esta ação é importante porque habilita/desabilita recursos exclusivos de cada ambiente.
         * Por exemplo, no ambiente de desenvolvimento, por padrão, todos os logs e avisos estão ativados.
         * 
         * Ambientes disponíveis:
         * test() Ambiente de testes.
         * prod() Ambiente de produção.
         */
        Application::dev();//Indica que está no ambiente de desenvolvimento
        
        //Inicializa a aplicação:
        Application::setup();         
        
    } catch(Exception $e) {
        echo 'Infelizmente não foi possível completar sua requisição: '.$e->getMessage();
    }
?>
