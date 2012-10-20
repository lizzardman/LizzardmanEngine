<?php

class Form {

    private $widgets;
    private $name;
    private $errors;
    private $value;



    public function codeToString($code){
           return ucfirst(strtolower(str_replace("_"," ", $code)));
    }

    public function __construct($name, $widgets, $value = " ") {

        $this->value = $value ;
        $this->errors = array();
        $this->name = $name;
        //create widgets from dictionary given in formExtension constructor
        foreach ($widgets as $key => $value) {
            $label = isset($value['label']) ? $value['label'] : NULL;
            $_value = isset($value['value']) ? $value['value'] : NULL;
            $regex = isset($value['regex']) ? $value['regex'] : NULL;
            $tip = isset($value['tip']) ? $value['tip'] : NULL;
            $required = isset($value['required']) ? $value['required'] : false;
            $this->widgets[$key] = new Formitem($value['type'], $key, $value['params'], $label, $_value, $regex, $tip, $required);
        }
    }

    //Get value of specified field
    function get($field) {
        $data = $this->getData();
        return $data[$field];
    }




    //Get form name
    function getName() {
        return $this->name;
    }

    //Set value of specified field
    function setValue($widget, $value) {
        $this->widgets[$widget]->setValue($value);
    }

    //Set error for specified field. It will be displayed under field widget
    function setErrorForWidget($widget, $error) {
        $this->widgets[$widget]->setError($error);
    }

    //Set errror for form. It will be displayed in form widget.
    function setErrors($errors) {
        if (is_array($errors)) {
            $this->errors = array_merge($this->errors, $errors);
        }
        else $this->errors[] = $errors;
    }

    //Check if forms has errors, Or any widgets contains errors. 
    // Validation is done in widgets isValid() method and in formExtention file
    function isValid($data = NULL) {


        $valid = true;

        if($this->widgets["disabled"]=="disabled"){
            return true;
        }

        if($data){
            if(isset($data->Errors)){
                foreach($data->Errors as $error){
                    // new ArrayIterator(&$error);
                        $this->setErrors(FormErrors::getInstance()->getUserError($error));
                }
            }
        }

        foreach ($this->widgets as $widget) {
            if (!$widget->isValid()) {
//                if($this->widgets["disabled"]=="disabled"){
//                    return true;   break;
//                }
                $valid = false;


            }
        }

        if (count($this->errors)) {

            $valid = false;
        }

        return $valid;
    }

    //Binds data of given array to form widgets.
    function bindData($post) {
        foreach ($this->widgets as $widget) {
            if (array_key_exists($widget->getName(), $post))
                $widget->setValue($post[$widget->getName()]);
        }
    }

    //Get an array of data "widget name -> widget value";
    function getData() {
        $data = array();

        foreach ($this->widgets as $widget)
            $data[$widget->getName()] = $widget->getValue();

        return $data;
    }

    //Gets view of form
    function getView() {

        return "<form method='post' action='?action=" . $this->name . "_submit' name='" . $this->name . "'>" .
                $this->getErrorsView() .
                "<table>" .
                $this->getWidgetsView() .
                "</table>" .
                $this->getButtonView() .
                "</form>";
    }

    //Gets view of widget
    function getWidgetView($name) {
        return $this->widgets[$name]->getView();
    }

    //Gets widget.
    function getWidget($name) {
        return $this->widgets[$name];
    }

    //Renders widget.
    function renderWidget($name) {
        echo $this->getWidgetView($name);
    }

    //Gets the form submit button view
    function getButtonView() {
        return ($this->value) ? "<input type='submit' value='" . $this->value . "' name='" . $this->name . "_submit'></input>" : "<input type='submit' name='" . $this->name . "_submit'></input>";
    }

    //Renders form submit button view
    function renderButton() {
        echo $this->getButtonView();
    }

    //Gets the view of all widgets
    function getWidgetsView() {
        $widgets = "";
        foreach ($this->widgets as $widget) {
            $widgets = $widgets . "<tr>" .
                    $widget->getView() .
                    "</tr>";
        }
       return $widgets;
    }

    //Renders whole form
    function render() {
        echo $this->getView();
    }

     function disableField($key){
             $this->widgets[$key]->addAttr("disabled", "disabled");
     }

    function disableFields () {
        foreach ($this->widgets as $key => $value) {
            if(!$value->getError()){
                $value -> addAttr("disabled", "disabled");
            }
        }
    }


    //Gets wiev of form errors
    function getErrorsView() {
        $errors_widget = "";

        if (count($this->errors)) {
            foreach ($this->errors as $error)              {
                $errors_widget = $errors_widget . "<div class='error'>" . $error . "</div>";
            }
            return '<div class="errortext title-error" style="height:15px">' . $errors_widget . "</div>";
        }
    }

    //Renders form errors
    function renderErrors() {
        echo $this->getErrorsView();
    }

}

class Formitem {

    private $name;
    private $params;
    private $type;
    private $label;
    private $value;
    private $regex;
    private $error;
    private $tip;
    private $required;

    public function __construct($type, $name, $params, $label = NULL, $value = NULL, $regex = NULL, $tip = NULL, $required=false) {
        $this->name = $name;
        $this->params = $params;
        $this->type = $type;
        $this->label = $label;
        $this->regex = $regex;
        $this->tip = $tip;
        $this->required= $required;
        if ($value)
            $this->setValue($value);
    }

    //Validation of widget
    function isValid() {

         if($this->params['attrs']["disabled"]=="disabled"){
             return true;
         }
        //Check widget value
        if ($this->required && !($this->value)) {
            $this->error = "Oops! Please enter your " . strtolower($this->label);
        } else {
            if ($this->regex!=null && !preg_match($this->regex, $this->value) && $this->value) {
                $this->error = "Oops! Please enter a valid " . strtolower($this->label);
            }
        }
        
        
        //Check if widget has errors
        if ($this->error)
            return false;
        else
            return true;
    }

    function addAttr($key, $value){
          $this->params['attrs'][$key]=$value;
    }

    function getWidgetView() {
        $attrs = "";

        //checks and constructs html attributes
        if (isset($this->params['attrs'])) {
            foreach ($this->params['attrs'] as $key => $value)
                $attrs = $attrs . $key . "='" . $value . "' ";
        }



        //construct widget html representation based on type
        switch ($this->type) {
            case "select":
                $options = "";
                
                foreach ($this->params['options'] as $key => $value) {
                    if ($value == $this->value) {
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }

                    $options = $options . "<option ". $selected ." value='" . (string)$value . "'>" . $key . "</option>";
                }
                $widget = "<select " . "name=" . $this->name . " " . $attrs . ">" . $options . "</select>";
                break;

            case "radio":
                if (!$this->value) {
                    $defval = true;
                }
                $widget = "";
                foreach ($this->params['options'] as $key => $value) {
                    if ($defval || $value == $this->value) {
                        $checked = "checked";
                    } else {
                        $checked = "";
                    }

                    $widget = $widget . "<input type=" . $this->type . " value=" . $value . " name=" . $this->name . " " . $attrs . " " . $checked . "></input><span class='blackfont'>" . $key . "</span><br>";
                    $defval = false;
                }

                break;

            case "textarea":
                $widget = "<textarea name=" . $this->name . " " . $attrs . ">" . $this->getValue() . "</textarea>";
                break;

            case "checkbox":
                $value = ($this->value) ? "checked=checked" : "";
                $widget = "<input type=" . $this->type . " value='1' name=" . $this->name . " " . $attrs . " " . $value . "></input>";
                break;

            default:
                $value = ($this->value) ? "value='" . $this->value ."'" :  "";
                $widget = "<input type=" . $this->type . " name=" . $this->name . " " . $attrs . "  ". $value ."></input>";
                break;
        }


        return $widget;
    }

    //constructs label html representation
    function getLabelView() {
        $label_widget = ( $this->label === NULL) ? "" : "<label class='blackfont' for='" . $this->name . "'>" . $this->label . "</label>";

        return $label_widget;
    }

    //constructs errors hmls representation
    function getErrorsView() {
        $errors_widget = "";
        if (isset($this->error)) {
            $errors_widget = $errors_widget . "<div class='errortext' >" . $this->error . "</div>";
        }
        return $errors_widget;
    }

    //constructs tip html representation if any
    function getTipView() {
        if ($this->tip) {
            return '<div class="darkfont">' . $this->tip . '</div>';
        }
        else
            return '';
    }

    //Sets an error for widget
    function setError($error) {
        $this->error = $error;
    }

    //Gets an widget error text
    function getError() {
        return $this->error;
    }

    //Gets a html of whole widget i.e label, widget, errrer, (tip)
    function getView() {
        return '<div class="loandivsector">' . $this->getLabelView() .
                $this->getTipView() .
                $this->getWidgetView() .
                $this->getErrorsView() . '</div>';
    }

    //Sets html attribute for widget
    function setAttr($key, $value) {
        $this->params['attrs'][$key] = $value;
    }



    //Gets the value of a widget
    function getValue() {

        return $this->value;
    }

    //Sets value for widget
    function setValue($value) {
        $this->value = $value;
    }

    //Gets a name os widget
    function getName() {
        return $this->name;
    }

    //render html representation of widget
    function render() {
        echo $this->getView();
    }

}

class FormErrors{

    protected static $instance;
    private $errors;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function __construct() {
        $this->errors=array();
    }

    public function getUserError($error){
        //var_dump($error->Name);
         return (isset($this->errors[$error->Name]))?   $this->errors[$error->Name]:  $error->Name;
    }
}

?>
