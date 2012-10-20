<?php





class View {

    public static function render($data, $template) {
        //Creates $_VIEW varible available in all templates by naming conbvention
        $_VIEW = $data;

        if (isset($_SESSION['flash'])) {
            $_VIEW['flash'] = $_SESSION['flash'];
            unset($_SESSION['flash']);
        } else {
            $_VIEW['flash'] = "";
        }

                //Presents template
        include "template/".$template;
    }

}

?>