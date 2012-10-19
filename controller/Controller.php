<?php

class Controller {

    protected static $instance;

    protected function __construct() {
        
    }

    public static function getInstance() {

        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

   public static function delete(){
       self::$instance=null;
   }

}

?>
