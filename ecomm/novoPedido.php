<?php

    include('class/Curl.php');
    include('class/BmXml.php');
    include('class/BmXmlInterface.php');
    include('class/BmFormaPgto.php');
    include('class/BmBoleto.php');
    include('class/BmCartaoDeCredito.php');
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
    try {
        $cartao = 0;
        if ($cartao == 1) {
            $objMeioPgto = new BmCartaoDeCredito();
            /*            
            $objMeioPgto->setBandeira('visa');
            $objMeioPgto->setNumCc('1236543956798756');
            $objMeioPgto->setCodSeg(132);
            $objMeioPgto->setValidadeCc(201509);
            $objMeioPgto->setConvenio('redecard');             
             */
            
            $objMeioPgto->setCc('visa', '1236543956798756', 123, 201408); 
            $objMeioPgto->setParcelas(6);
            $objMeioPgto->capturaOn();
        } else {
            $objMeioPgto = new BmBoleto();
            $objMeioPgto->Bradesco();
            $objMeioPgto->setDiasVencimento(10);        
        }
    } catch (Exception $e) {
        die($e->getMessage());
    }
    //$objPedido->debugOn();
    
    $objPedido->getTotalPedido();
    $objPedido->persistSacadoOn();
    $objPedido->checkout($objMeioPgto);
    
    //Incluir itens/produtos ao pedido:
    $objItemA = new BmItemPedido('Assinatura/superpro','PL400','Plano 400',297.5,3,'ASS','Natal 2013');
    $objItemA->persistItemOn();

    $objItemB  = new BmItemPedido('Assinatura/superpro','PL800','Plano 800',396);
    $objItemC  = new BmItemPedido('Assinatura/superpro','PL1800','Plano 1800',412.543,5);
    
    try {
        //Incluir itens ao pedido atual:
        $objPedido->addItemPedido($objItemA);
        $objPedido->addItemPedido($objItemB);
        $objPedido->addItemPedido($objItemC);
        $objPedido->printXml();        
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
