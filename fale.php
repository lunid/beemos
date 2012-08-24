<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html class="no-js" lang="en"> <!--<![endif]--><head>

        <!-- Basic Page Needs
  ================================================== -->
        <meta charset="utf-8">
        <title>Super Professor Web</title>
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Mobile Specific Metas
  ================================================== -->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <!-- CSS
  ================================================== -->
        <link rel="stylesheet" href="stylesheets/base.css">
        <link rel="stylesheet" href="stylesheets/skeleton.css">
        <link rel="stylesheet" href="stylesheets/layout.css">
        <link rel="stylesheet" href="stylesheets/style.css">
        <link rel="stylesheet" href="stylesheets/pg-internas.css">
        
        <!--[if lt IE 9]>
                <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- Favicons
        ================================================== -->
        <link rel="shortcut icon" href="images/favicon.ico">
        <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
        <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
        
        
        <script type="text/javascript" src="js/libs/jquery_171.js"></script>
        <script type="text/javascript" src="js/libs/slider/coin-slider.min.js"></script>
        <script type="text/javascript" src="js/libs/menu/magic-line.js"></script>
        
        <script type="text/javascript" src="js/libs/util/dictionary.js"></script>
        <script type="text/javascript" src="js/libs/util/menu.js"></script>
        <script type="text/javascript" src="js/libs/util/slider.js"></script>
        <script type="text/javascript" src="js/home.js"></script>
        
        <script type="text/javascript">
            $(document).ready(function() {
                home = new Home();
                home.init();
            });
        </script>


<?
    require_once 'class/suporte.php';
    
    if($_POST){
        $suporte = new Suporte();
        
        $suporte->setNome($_POST['nome']);
        $suporte->setEmail($_POST['email']);
        $suporte->setCategoriaId($_POST['categoria_id']); 
        $suporte->setMensagem($_POST['msg']);
        
        if($suporte->save()){
            echo "E-mail enviado com sucesso!<br /><br />";
        }
    }
    
    $categoria  = new Categoria();
    $rs         = $categoria->listaCategorias();
?>


        
       
    </head>
    <body>
        <div id="bg">

            <!-- Primary Page Layout
            ================================================== -->

            <!-- Delete everything in this .container and get started on your own site! -->

            <!-- top -->
             <?php
			include "top.php";
			?>
            <!-- fim top -->
            <!-- começo menu -->
            <div class="top">
            <?php
			include "menu.php";
			?>
            </div>
            <!-- fim menu -->
            <!-- começo pagina interna -->
            <div class="container">
            	<div class=".four.columns">
                	<div class="menu-lateral">
                    <div id="tit">
                    <div class="icons3"></div>
                    <div class="titulo-menu">Ajuda</div>
                    </div>
                    <div class="top-menu"></div>
                      <?php
			include "menu-ajuda.php";
			?>
					<div class="botton-menu"></div>
                    </div>
                 
                </div>
                <div class=".eight.columns">
                	<div class="pg">
                     	<div class="testeira"><img src="images/testeira.jpg"></div>
                        <div class="migalha">Ajuda >> Fale com o Suporte</div>
                        <div class="titulo">Fale com o Suporte</div>
                        <div class=".six.columns">
                        <div class="formulario-contato">
                         <form id="form_suporte" name="form_suporte" action="form_suporte.php" method="post" onsubmit="return form.validate(this);">
                        <p>Nome:</p>
                        <input type="text" id="nome" name="nome" class="input_text required" field_name="Nome" tip="Aqui você preenche seu nome completo" />
                        <p>E-mail:</p>
                        <input type="text" id="email" name="email" class="input_text required email" field_name="E-mail" tip="Utilize um e-mail válido" />
                        <p>Categoria:</p>
                         <?
                            if($rs){
                        ?>
                        <select id="categoria_id" name="categoria_id" class="required" field_name="Categoria">
                            <option value="0">Selecione uma categoria</option>
                            <?
                                while ($row = mysql_fetch_object($rs)) {
                            ?>
                            <option value="<?=$row->CATEGORIA_ID?>"><?=$row->DESCRICAO?></option>
                            <?
                                }
                            ?>
                        </select>
                        <?
                            }
                        ?>
                        <p>Mensagem:</p>
                        <br />
                        <textarea id="msg" name="msg" class="textarea required" field_name="Mensagem"></textarea>
                        <p><input type="submit" value="Enviar" /></p>
        				</form>
        				</div>
             <script type="text/javascript">
            $(document).ready(function() {
                $.post(
                    "dic/pt/javascript.xml",
                    null,
                    function(xml){
                        xml_dic = xml;
                        form = new Form();
                        form.init('form_suporte');
                    },
                    'xml'
                );                
            });
        </script>

                        
                        </div>
                        <div class=".six.columns">
                        <div class="foto-inter"><img src="images/foto_inter.png"></div>
                        <div class="texto-pequeno">
                   <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat,</p></div>
                        
                        </div>
                   </div>
                  </div>
                </div>
            </div>
            <!-- fim pagina interna -->
            <!-- rodape -->
			 <?php
			include "rodape.php";
			?>
            <!-- fim rodape -->
            <!-- End Document
            ================================================== -->
        </div>
    </body>
</html>