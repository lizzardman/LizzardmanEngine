<?php

require_once "form.php";



class FormIndex extends Form {

    public function __construct() {
        parent::__construct("index", array());
    }

    public function isValid($data = NULL) {
        return parent::isValid($data);
    }





}

?>
