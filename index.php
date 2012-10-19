<?php
    session_start();
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', true);    
    include('sys/classes/_init/Application.php');
    
    try {
        //Inicializa a aplicação:
        Application::setup();   
    } catch(Exception $e) {
        //echo 'Erro ao efetuar o setup da aplicação: '.$e->getMessage();
    }
?>
