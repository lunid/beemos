<?php
session_start();

if($_POST){
    $rede = @$_POST["hdd_rede"];
}
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Home</title>

        <link rel="stylesheet" type="text/css" href="js/libs/slider/coin-slider-styles.css" />
        <link rel="stylesheet" type="text/css" href="js/libs/menu/style.css" />
        <link rel="stylesheet" type="text/css" href="js/libs/fancybox/jquery_fancybox.css" />

        <script type="text/javascript" src="js/libs/jquery_171.js"></script>
        <script type="text/javascript" src="js/libs/slider/coin-slider.min.js"></script>
        <script type="text/javascript" src="js/libs/menu/magic-line.js"></script>
        <script type="text/javascript" src="js/libs/fancybox/jquery_fancybox_pack.js"></script>
        
        <script type="text/javascript" src="js/libs/util/dictionary.js"></script>
        <script type="text/javascript" src="js/libs/util/menu.js"></script>
        <script type="text/javascript" src="js/libs/util/slider.js"></script>
        <script type="text/javascript" src="js/home.js"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                var home = new Home();
                home.init();
            });
        </script>
    </head>
    <body>
        <table style="width:100%;">
            <tr>
                <td>
                    <div class="nav-wrap">
                        <ul class="group" id="top-menu">
                            <li id="menu_home" class="current_page_item" onclick="javascript:menu.changeMenu(this.id);">
                                <a href="javascript:void(0);">Home <br /> Teste</a>
                            </li>
                            <li id="menu_cursos" onclick="javascript:menu.changeMenu(this.id);"><a href="javascript:void(0);">Cursos</a></li>
                            <li id="menu_planos" onclick="javascript:menu.changeMenu(this.id);"><a href="javascript:void(0);">Planos</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="980px;" align="center">
                    <div id='slider' style="width:980px;height:400px;text-align:center;">
                        <a href="www.uol.com.br" target="_blank">
                            <img src='img_slider/slider_01.jpg' >
                        </a>
                        <a href="imgN_url">
                            <img src='img_slider/slider_02.jpg' >
                        </a>
                        <a href="imgN_url">
                            <img src='img_slider/slider_03.jpg' >
                        </a>
                    </div>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <!-- Login -->
                    <form name="form_login" id="form_login" method="post" action="index.php">
                        <table align="center">
                            <tr>
                                <td>
                                    Login:
                                </td>
                                <td>
                                    <input type="text" name="login" id="login" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Senha:
                                </td>
                                <td>
                                    <input type="text" name="senha" id="senha" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="right">
                                    <a href="javascript:void(0);" onclick="javascript:window.open('http://localhost/home/rede.php?rede=google', 'Rede', 'width=600,height=400,scrollbars=1');">
                                        <img src="img/facebook-icon.png" style="width:20px;height:20px;" />
                                    </a>
                                    <input type="hidden" name="hdd_rede" id="hdd_rede" value="" />
                                </td>
                            </tr>
                        </table>    
                    </form>
                    <!-- Login -->
                </td>
            </tr>
        </table>
    </body>
</html>
