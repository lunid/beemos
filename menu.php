<div class="container nav-wrap">
       <ul class="group" id="top-menu">
                        <li id="menu_home" <?php if(@$_GET["pg"] == "home" || !isset($_GET["pg"])){ ?>class="current_page_item"<? } ?> onclick="javascript:menu.changeMenu(this.id);">
                            <a href="index.php?pg=home">Home<p>Bem Vindo</p></a>
                        </li>
                        <li id="menu_sobre" <?php if(@$_GET["pg"] == "sobre"){ ?>class="current_page_item"<? } ?> onclick="javascript:menu.changeMenu(this.id);">
                            <a href="ainterbits.php?pg=sobre">Sobre Nós<p>Conheça a InterBits</p></a>
                        </li>
                        <li id="menu_assine" <?php if(@$_GET["pg"] == "assine"){ ?>class="current_page_item"<? } ?> onclick="javascript:menu.changeMenu(this.id);">
                            <a href="assineja.php?pg=assine">Assine Já<p>Planos & Preços</p></a>
                        </li>
                         <li id="menu_super" <?php if(@$_GET["pg"] == "super"){ ?>class="current_page_item"<? } ?> onclick="javascript:menu.changeMenu(this.id);">
                            <a href="super.php?pg=super">SuperPro<p>Principais Recursos</p></a>
                        </li>
                         <li id="menu_ajuda" <?php if(@$_GET["pg"] == "ajuda"){ ?>class="current_page_item"<? } ?> onclick="javascript:menu.changeMenu(this.id);">
                            <a href="ajuda.php?pg=ajuda">Ajuda<p>FAQ, Tutorias e suporte</p></a>
                        </li>
     	</ul>
</div>