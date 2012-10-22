<?php

require_once "view.php";
require_once "formExtentions.php";
require_once 'user.php';

class IndexController extends Controller {
    public static function getInstance() {

        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function viewAction($data=null) {
        View::render(array('name'=>$data['name']), 'index');
    }
}

?>
