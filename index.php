<?php
    session_start();
    //error_reporting(E_ALL | E_STRICT);
    //ini_set('display_errors', true);     
    
    include('sys/classes/_init/Application.php');
     
    try {
        //Inicializa a aplicação:
        Application::setup();   
    } catch(Exception $e) {
        echo 'Infelizmente não foi possível completar sua requisição: '.$e->getMessage();
    }
?>
