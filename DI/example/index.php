<?php

    require_once("../src/DI.php");
    
    class View {
        public function show($str) {
            echo "<p>".$str."</p>";
        }
    }
    
    class UsersModel {
        public function get() {
            return array(
                (object) array("firstName" => "John", "lastName" => "Doe"),
                (object) array("firstName" => "Mark", "lastName" => "Black")
            );
        }
    }
    
    /**
    * @Inject view
    */
    class Navigation {
        public function show() {
            $this->view->show('
                <a href="#" title="Home">Home</a> | 
                <a href="#" title="Home">Products</a> | 
                <a href="#" title="Home">Contacts</a>
            ');
        }
    }
    
    /**
    * @Inject usersModel
    * @Inject view
    */
    class Content {
        private $title;
        public function __construct($title) {
            $this->title = $title;
        }
        public function show() {  
            $this->users = $this->usersModel->get();
            $this->view->show($this->title);
            foreach($this->users as $user) {
                $this->view->show($user->firstName." ".$user->lastName);
            }
        }
    }
    
    /**
    * @Inject navigation
    * @Inject content
    */
    class PageController {
        public function show() {
            $this->navigation->show();
            $this->content->show();
        }
    }
    
    // mapping
    DI::mapClass("navigation", "Navigation");
    DI::mapClass("content", "Content", array("Content title!"));
    DI::mapClass("view", "View");
    DI::mapClassAsSingleton("usersModel", "UsersModel");
    
    // showing content
    $page = DI::getInstanceOf("PageController");
    $page->show();
    

?>