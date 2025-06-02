<?php
    namespace controllers;

    class ErrorController {
        public function index(){
            echo "<h1>Esta p&aacute;gina no existe</h1>";
            echo '<a href="' . BASE_URL . '" class="boton boton-volver">Ir al inicio</a>';
        }
    }
?>