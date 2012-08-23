<?php
class Email{
    private $de;
    private $nome_de;
    private $para;
    private $assunto;
    private $html_msg;
    
    public function enviaContato($nome, $email, $assunto, $mensagem){
        try{
            $html = "
                <html>
                    <body>
                        <table width='100%' cellpadding='2' cellspacing='1' style='border:1px solid #CCC;'> 
                        <tr>
                            <td nowrap='nowrap' width='1%'>
                                <b>
                                    Nome:
                                </b>
                            </td>
                            <td nowrap='nowrap' width='99%'>
                                ".$nome."
                            </td>
                        </tr>
                        <tr>
                            <td nowrap='nowrap' width='1%'>
                                <b>
                                    E-mail:
                                </b>
                            </td>
                            <td nowrap='nowrap'>
                                ".$email."
                            </td>
                        </tr>
                        <tr>
                            <td nowrap='nowrap' width='1%'>
                                <b>
                                    Assunto:
                                </b>
                            </td>
                            <td nowrap='nowrap'>
                                ".$assunto."
                            </td>
                        </tr>
                        <tr>
                            <td nowrap='nowrap' width='1%' colspan='2'>
                                <b>
                                    Mensagem:
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap='nowrap' colspan='2'>
                                ".$mensagem."
                            </td>
                        </tr>
                        </table>
                    </body>
                <html>
            ";
            
            $this->de       = $email;
            $this->nome_de  = $nome;
            $this->para     = "prg.pacheco@interbits.com.br";
            $this->assunto  = "[E-MAIL DE CONTATO] SuperProWeb";
            $this->html_msg = $html;
            
            return $this->enviaEmail();
        }catch(Exception $e){
            throw $e;
        }
    }
    
    private function enviaEmail(){
        try{
            $from           = $this->de;
            $fromName       = $this->nome_de; 
            $system_name    = "SuperProWeb";

            $elements = explode("@", $from);
            if (2 == sizeof($elements)){
                $domain = $elements[1];
            }else{
                throw new Exception("Email $from is not a valid address<br/><br/>\n\n");
            }

            $mimeBoundary = "==MULTIPART_BOUNDARY_" . md5(time());
            // Headers
            $headers = "MIME-Version: 1.0\n";
            $headers.= "Content-type: multipart/alternative;\n     boundary=\"" . $mimeBoundary . "\"\n";
            $headers.= "Organization: " . $system_name . "\n";
            $headers.= "From: $fromName <$from>\n";
            $headers.= "Reply-To: $from\n";
            $headers.= "Message-ID: <" . md5(uniqid(time())) . "@$domain>\n";
            $headers.= "Return-Path: $from\n";
            $headers.= "X-Priority: 3\n";
            $headers.= "X-MSmail-Priority: Normal\n";
            $headers.= "X-Mailer: " . $system_name . " Mailer V1.1\n";
            $headers.= "X-MimeOLE: Produced By " . $system_name . " MimeOLE V1.1\n";
            $headers.= "X-Sender: $from\n";

            $textMessage = strip_tags(str_replace("<hr/>", "-----------------------------------------------------------", $this->html_msg));
            $body = "This is a multi-part message in MIME format.\n";
            $body.= "\n";
            $body.= "--" . $mimeBoundary . "\n";
            $body.= "Content-Type: text/plain; charset=utf-8\n";
            $body.= "Content-Transfer-Encoding: 7bit\n";
            $body.= "\n";
            $body.= $textMessage . "\n";
            $body.= "\n";
            $body.= "--" . $mimeBoundary . "\n";
            $body.= "Content-Type: text/html; charset=utf-8\n";
            $body.= "Content-Transfer-Encoding: 7bit\n";
            $body.= "\n";
            $body.= "{$this->html_msg}\n";
            $body.= "\n";
            $body.= "--" . $mimeBoundary . "--\n";

            return mail($this->para, $this->assunto, $body, $headers);
        }catch(Exception $e){
            throw $e;
        }
    }
}
?>
