<?php

require_once 'controller/Controller.php';
    require_once 'form.php';
    require_once 'user.php';

class Router {

    public $Routes;
    protected static $instance;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function __construct() {
        $this->Routes = array();
        
        //User routes
        $this->addRequest("index", Role::Anonymus, 'Index?view');

    }

    //Construct route
    private function addRequest($action, $role, $route) {
        //Adding requst for ?action=xxx key
        //Depend on route and progress
        $this->Routes[$action] = new Route($role, $route);
    }


    //Internal redirecting method
    public function redirect($controller, $action, $data = null) {
        Controller::delete();
        
        //Geting controller classname
        $classname = $controller . 'Controller';
        
        //Including controller code
        require_once "controller/" . $classname . '.php';
        
        //Calling function $action+Action with given $data;
        call_user_func(array(call_user_func(array($classname, 'getInstance')), $action . 'Action'), $data);
        die;
    }

    public function headerRedirect($action, $vars = array()) {
        //Simply seting header("location:") for given request as if user entered it
        $vars = (count($vars)) ? ($action) ? "?" . http_build_query(array_merge(array("action" => $action), $vars)) : "?" . http_build_query($vars)  : "";
        header("location: index.php" . $vars);
        die;
    }

    public function getDefaultRoute() {
        return "index";
    }
    
    public function request($request) {
        //Geting current user
        $user = User::getInstance();
        //If correct request is entered and found in Route array
        if (isset($request['action']) && isset($this->Routes[$request['action']])) {
            $route = $this->Routes[$request['action']];

            //Checks if user has enouth role and progress for evaluting requst
            if (($user->getRole() >= $route->getMinRole())) {
                $this->redirect($route->getController(), $route->getAction(), $request);
                
            } else {
                // TODO: Redirect to default request
                $route = $this->Routes[$this->getDefaultRoute()];
                $this->redirect($route->getController(), $route->getAction(), $request);
            }
        } else {
            //If requst if bad redirecting to default request
            $route = $this->Routes[$this->getDefaultRoute()];
            $this->redirect($route->getController(), $route->getAction(), $request);
        }
    }
    


}

class Route {

    private $action;
    private $controller;
    private $minRole;


    public function __construct($minRole, $route) {
        $this->minRole = $minRole;
        $contr = explode("?", $route);
        $this->controller = $contr[0];
        $this->action = $contr[1];
     }

    public function getcontroller() {
        return $this->controller;
    }

    public function getAction() {
        return $this->action;
    }

    public function getMinRole() {
        return $this->minRole;
    }

}

?>
