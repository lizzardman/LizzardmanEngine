<?php

class User
{
    protected static $instance;
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    protected function __construct() {
        if(!isset($_SESSION['user'])){
            $_SESSION['user']=array();
        }

        if(!isset($_SESSION['user']['role']))  {
            $_SESSION['user']['role'] = Role::Anonymus;
        }
    }
    

    function getRole() {
        return $_SESSION['user']['role'];
    }
    
    function setRole($role){
        $_SESSION['user']['role'] = $role;
    }
    

    
}

class Role {
    const Anonymus = 0;
    const Loggedin = 1;
}





?>
