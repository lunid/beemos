<?php

    namespace admin\classes\views;
    
    use \sys\classes\mvc\View;     
    use \admin\classes\html\MenuVertical;
    
    class ViewAdmin extends View {
                
        function render($layoutName=''){
<div id='menu'><p>Titulo</p></div>
                <ul class='sub-menu'>
                    <li><a class='home' href='index.html'>Home</a></li>
                    <li><a class='usuario' href='usuario.html'>Usuário</a></li>
                    <li><a class='comercial' href='comercial.html'>Comercial</a></li>
                    <li><a class='cliente' href='cliente.html'>Clientes</a></li>
                    <li><a class='relatorio' href='relatorios.html'>Relatórios</a></li>
                </ul>  
            
            $objMenuVert    = new MenuVertical();
            $objMenuVert->addItem('home','Home');
            $objMenuVert->addItem('usuarios','Usuários');
            $objMenuVert->addItem('comercial','Comercial');
            $objMenuVert->addItem('clientes','Clientes');
            $objMenuVert->addItem('relatorios','Relatorios');            
            
            $this->MENU_VERTICAL = $objMenuVert->render($layoutName);
            parent::render($layoutName);
        }
    }
?>
