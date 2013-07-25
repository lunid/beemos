<?php

namespace commerce\classes\helpers;

class XmlRequestNovoPedidoHelper extends XmlRequestHelper {
    
    static  $arrParamsReq       = array('BANDEIRA','NUM_CARTAO','COD_SEGURANCA','VALOR_COMPRA','VLD_MES','VLD_ANO','PARCELAS','NUM_PEDIDO');//Parâmetros obrigatórios
}

?>
