<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BmDadosSac
 *
 * @author Claudio
 */
class BmSacado extends BmXml {
    
    private $nome;
    private $email;
    private $logradouro;
    private $cidade;
    private $uf;
    private $cpfCnpj;
    
    function __construct($nome='',$email='',$logradouro='',$cidade='',$uf='',$cpfCnpj=''){
        $this->setNome($nome);
        $this->setEmail($email);
        $this->setLogradouro($logradouro);
        $this->setCidade($cidade);
        $this->setUf($uf);
        $this->setCpfCnpj($cpfCnpj);
    }  

    public function setNome($value){
        $value = trim($value);
        if (strlen($value) > 0){
            $this->nome = $value;            
        }          
        $this->addParamXml('NOME',$this->nome);
    }

    public function setEmail($value){
        $value = trim($value);
        if (strlen($value) > 0){
            $this->email = $value;            
        }    
        $this->addParamXml('EMAIL',$this->email);
    }

    /**
     * Define o logradouro do sacado.
     * Pode conter nome da rua/logradouro, número, bairro e complemento, se houver.
     * 
     * Exemplo:
     * <code>
     *  $objSacado = new Sacado();
     *  $objSacado->setLogradouro('av. das Acácias, 3043 Jardim das Amoras, sala 21');
     * </code>
     * 
     * @param string $value Deve conter string com nome da rua/logradouro, número, bairro e complemento
     * @return void
     */
    public function setLogradouro($value){
        $value = trim($value);
        if (strlen($value) > 0){
            $this->logradouro = $value;            
        }        
        $this->addParamXml('ENDERECO',$this->logradouro);
    }
    
    /**
     * Permite informar a cidade e o estado em uma única string.
     * O formato esperado é cidade/uf.
     * 
     * Exemplo:
     * <code>
     *  $objSacado = new Sacado();
     *  $objSacado->setCidadeUf('São Paulo/SP');
     * </code>
     * 
     * @param string $value Deve conter uma string no formato cidade/UFS
     * @return void
     */
    public function setCidadeUf($value){
        if (strlen($value) > 0) {
            @list($cidade,$uf) = @explode('/',$value);
            if (strlen($cidade) > 0) $this->setCidade($cidade);
            if (strlen($uf) > 0) $this->setUf($uf);
        }
    }

    public function setCidade($value){
        $value = trim($value);
        if (strlen($value) > 0){
            $this->cidade = $value;            
        }
        $this->addParamXml('CIDADE',$this->cidade);
    }

    public function setUf($value){
        $value = trim($value);
        if (strlen($value) == 2 && ctype_alpha($value)) {
            $value          = strtoupper($value);
            $this->uf    = $value;            
        }
        $this->addParamXml('UF',$this->uf);
    }

   /**
    * Recebe e guarda um valor referente a um CPF (11 dígitos) ou a um CNPJ (14 dígitos).
    * 
    * @param string $value
    */
    function setCpfCnpj($value){
        $value = trim($value);
        if (strlen($value) > 0) {
            //Retira caracteres que não sejam numéricos:
            $arrValue   = str_split($value);
            $arrChar    = array();
            
            foreach ($arrValue as $char){
                if (ctype_digit($char)) $arrChar[] = $char;//Armazena apenas caracteres numéricos.
            }

            $valueChar = join('',$arrChar);

            if (strlen($valueChar) >=11 && strlen($valueChar) <= 14 && ctype_alnum($valueChar)) {
                $this->cpfCnpj = $valueChar;                
            } else {
                $msgErr = 'Pedido->setCpfCnpjSac() O CPF/CNPJ informado ('.$value.') parece ser inválido.';
                throw new \Exception($msgErr);
            }       
            $this->addParamXml('CPF_CNPJ',$this->cpfCnpj);
        }
    }  
    
    public function getXml(){      
        $save   = ($this->save) ? 1 : 0;
        $xml    = "<SACADO save='{$save}'>".$this->getTagParams()."</SACADO>";        
        return $xml;
    }       
}

?>
