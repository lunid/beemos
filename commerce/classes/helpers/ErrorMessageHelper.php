<?php

namespace commerce\classes\helpers;
class ErrorMessageHelper {
   
    public static function __callstatic($action,$arrParams){        
        $arrErr['INDEF']    = array();
        $code               = strtoupper($arrParams[0]);
        $arrReplaceVal      = (isset($arrParams[1]))?$arrParams[1]:array();
        
        switch($action) {
            case 'index':
                $i = 1;
                $arrErr['USER_NOT_EXISTS']          = array($i++,'Usuário não localizado.');
                $arrErr['USER_BLOQ']                = array($i++,'A assinatura informada está suspensa. Entre em contato com a Supervip para reativar o serviço.');
                $arrErr['ERR_ACTION_NOT_EXISTS']    = array($i++,'O parâmetro action "{ACTION_NAME}" não é válido.');
                $arrErr['ERR_ACTION_NOT_INFO']      = array($i++,'O parâmetro action não foi informado.');                
                $arrErr['ERR_HASH_ASS']             = array($i++,'O parâmetro uid ({HASH_ASSINATURA}) parece estar incorreto.');
                break;
        }
        
        if (count($arrErr) > 0 && @isset($arrErr[$code])) {
            $arrInfoErr             = $arrErr[$code];
            $codStatus              = (int)$arrInfoErr[0];
            $msg                    = $arrInfoErr[1];
            
            if (is_array($arrReplaceVal)) {
                foreach($arrReplaceVal as $key=>$replace){
                    $key        = strtoupper($key);
                    $find       = "{{$key}}";                    
                    $msg        = str_replace($find,$replace,$msg);
                }
            }
            
            $msgStatus              = "[{$action} -> COD={$codStatus}] ".$msg;
            $objXmlResponseHelper   = new XmlResponseHelper($codStatus,$msgStatus);
            $xmlResponse            = $objXmlResponseHelper->render();
            die($xmlResponse);
        }
    }
}

?>
