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
            <div class="container">
                <div id="banner">
                    <a href="#" target="_blank">
                        <img src='images/banner3.png' style="width:900px;height:365px;" />
                    </a>
                    <a href="#" target="_blank">
                        <img src='images/banner4.png' style="width:900px;height:365px;" />
                    </a>
                </div>
                <div class="five columns">
                    <div id="grafico">
                        <h1>98%</h1><h3>dos</h3>
                        <h2>nossos assinantes indicam</h2>
                    </div>
                </div>
                <div class="five columns">
                    <div class="perguntas">
                        <h1>100.667 </h1><span>Questões</span>
                        <h2>1.655 </h2><spam>ENEM</spam>
                    </div>
                </div>
                <div class="five columns">
                    <div class="experimente">
                        <h1>Experimente Já</h1><h2></h2>
                        <span>Grátis!</span>
                    </div>
                </div>
                <div class="five columns">
                    <div class="icon_home">
                        <h3>Motivos para Usar o SuperPro®</h3>
                    </div>
                    <div id="lista">
                        <p><span>1.</span> Lorem Ipsum</p>
                        <p><span>2.</span> Lorem Ipsum</p>
                        <p><span>3.</span> Lorem Ipsum</p>
                        <p><span>4.</span> Lorem Ipsum</p>
                        <p><span>5.</span> Lorem Ipsum</p>
                    </div>
                    <button class="buttonsaibamais">Saiba +</button>
                </div>
                <div class="five columns">
                    <div class="icon_home2">
                        <h3>Prepare sua Prova Rapidamente</h3>
                    </div>
                    <div id="lista2">
                        <p>Lorem ipsum dolor sit amet, elit, consectetuer adipiscing elit,</p>
                        <p>Lorem ipsum dolor sit amet, elit, consectetuer adipiscing elit,</p>
                        <p>Lorem ipsum dolor sit amet, elit, consectetuer adipiscing elit,</p>
                        <p>Lorem ipsum dolor sit amet, elit, consectetuer adipiscing elit,</p>
                        <p>Lorem ipsum dolor sit amet, elit, consectetuer adipiscing elit,</p>
                    </div>
                    <button class="buttonsaibamais">Veja o resultado</button>
                </div>
                <div class="five columns">
                    <div class="icon_home3">
                        <h3><span>Crie exercícios Online</span> Para seus Alunos</h3>
                    </div>
                    <div id="lista2">
                        <p>Lorem ipsum dolor sit amet, elit, consectetuer adipiscing elit,</p>
                        <p>Lorem ipsum dolor sit amet, elit, consectetuer adipiscing elit,</p>
                        <p>Lorem ipsum dolor sit amet, elit, consectetuer adipiscing elit,</p>
                        <p>Lorem ipsum dolor sit amet, elit, consectetuer adipiscing elit,</p>
                        <p>Lorem ipsum dolor sit amet, elit, consectetuer adipiscing elit,</p>
                    </div>
                    <button class="buttonsaibamais">Saiba +</button>
                </div>
            </div>
            <!-- container -->
			 <?php
			include "rodape.php";
			?>
            <!-- End Document
            ================================================== -->
        </div>
    </body>
</html>