<?php

    /** 
     * Controlador para manejar errores en la aplicación.
     * 
     * Métodos:
     * - index(): Muestra una página de error.
     */

    namespace controllers;

    class ErrorController {

        /**
         * Muestra un mensaje de error, el cual se debe mostrar cada vez que el usuario
         * intente acceder a una página que no existe o a una página a la que no tiene acceso según
         * las reglas definidas en el archivo .htaccess.
         */

        public function index(): void {
            echo "<script>document.title = 'Error - Página no encontrada';</script>";
            echo "<h1>La página que buscas no existe.</h1>";
            echo "<h1 style=\"font-size: 500%\">:(</h1>";
            echo "<a href='".BASE_URL."'><button class=\"boton\">Volver a la tienda</button></a>";
        }
    }

?>