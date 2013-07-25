<?php

    include('class/Curl.php');
    include('class/BmXml.php');    
    include('class/BmSacado.php');   
    include('class/BmBoleto.php');
    include('class/BmCartaoDeCredito.php');
    include('class/BmPedido.php');
    include('class/BmItemPedido.php');
    
    $nomeSac    = "Claudio Jo�o da Costa Aguiar D'�vila";
    $emailSac   = "claudio@supervip.com.br";
    $objSacado  = new BmSacado($nomeSac,$emailSac);
    
    //Define a forma de pagamento
    try {
        $cartao     = 1;
        $objPedido  = new BmPedido();
        $objPedido->addSacado($objSacado);   
        
        if ($cartao == 1) {
            //Pagamento com cart�o
            $objMeioPgto = new BmCartaoDeCredito('visa','1236543956798756',123,201308,7,'REDECARD');
            
            //FORMA 1:
            /*            
            $objMeioPgto->setBandeira('visa');
            $objMeioPgto->setNumCc('1236543956798756');
            $objMeioPgto->setCodSeg(132);
            $objMeioPgto->setValidadeCc(201509);
            $objMeioPgto->setConvenio('redecard');             
             */
            
            //FORMA 2:
            $objMeioPgto->setCc('visa', '1236543956798756', 123, 201408); 
            $objMeioPgto->setParcelas(6);
            $objMeioPgto->capturaOn();
        } else {
            $objMeioPgto = new BmBoleto();
            $objMeioPgto->Bradesco();
            $objMeioPgto->setDiasVencimento(10);        
        }
        
        $objPedido->addMeioPgto($objMeioPgto);

        //Incluir itens/produtos ao pedido:
        $objItemA = new BmItemPedido('Assinatura/superpro','PL400','Plano 400',297.5,250,3,'ASS','Natal 2013');
        $objItemA->persistOn();

        $objItemB  = new BmItemPedido('Assinatura/superpro','PL800','Plano 800',396);
        $objItemB->setQuantidade(7);
        $objItemB->setPrecoPromo(329.9);
        $objItemC  = new BmItemPedido('Assinatura/superpro','PL1800','Plano 1800',412.543,0,5);  
        $arrObjItens = array($objItemA,$objItemB,$objItemC);
        
        //Incluir itens ao pedido atual:
        $objPedido->addItensPedido($arrObjItens);          
        
        $objPedido->setFrete(327.43);
        //$objPedido->setTotalPedido(5000);
        $objPedido->setDesconto(350.23);
        //$objPedido->printXml();          
        $response = $objPedido->save();
        if ($response == TRUE) {
            
        } else {
            //Um erro ocorreu ao gravar o pedido
            echo $response;
        }
        
    } catch (\Exception $e) {
        die($e->getMessage());
    }

    //$objPedido->getTotalPedido();
    //$objPedido->persistSacadoOn();
     
?>
