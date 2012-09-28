<?php
    session_start();
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', true);    
    include('sys/classes/_init/Application.php');
    
    //Inicializa a aplicação:
    Application::load();
?>