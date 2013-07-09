<?php

    include('class/Curl.php');
    include('class/BmPedido.php');
    include('class/BmItemPedido.php');
    
    //Dados do cliente
    $arrDados    = array(
            'NOME_SAC'      => "Claudio João da Costa Aguiar D'ávila",
            'EMAIL_SAC'     => 'claudio@supervip.com.br',
            'ENDERECO_SAC'  => 'Rua Maestro Cardim, 1218 - apto 71 - Bela vista',
            'CIDADE_SAC'    => 'São Paulo',
            'UF_SAC'        => 'sp',
            'CPF_CNPJ_SAC'  => '04.067.415/0001-33',           
            'VALOR_FRETE'   => 34.43            
        );
    
    $objPedido      = new BmPedido($arrDados);//Valida e armazena os dados contidos em $arrDados;
    
    //Define a forma de pagamento
    $objFormaPgto   = new BmFormaPgto();
    $objFormaPgto->setBltBradesco();
    $objPedido->addFormaPgto($objFormaPgto);
    
    //$objPedido->debugOn();
    
    $objPedido->getTotalPedido();
    $objPedido->persistSacadoOn();
    
    //Incluir itens/produtos ao pedido:
    $objItemA = new BmItemPedido('Assinatura/superpro','PL400','Plano 400',297.5,3,'ASS','Natal 2013');
    $objItemA->persistItemOn();

    $objItemB  = new BmItemPedido('Assinatura/superpro','PL800','Plano 800',396);
    $objItemC  = new BmItemPedido('Assinatura/superpro','PL1800','Plano 1800',412.543,5);

    //Incluir itens ao pedido atual:
    $objPedido->addItemPedido($objItemA);
    $objPedido->addItemPedido($objItemB);
    $objPedido->addItemPedido($objItemC);
    //$objPedido->printXml();
    
    try {
        $response = $objPedido->savePedido();
        if ($response == TRUE) {
            
        } else {
            //Um erro ocorreu ao gravar o pedido
            echo $response;
        }
    } catch(Exception $e) {
        echo $e->getMessage();
        die();
    }       
?>
