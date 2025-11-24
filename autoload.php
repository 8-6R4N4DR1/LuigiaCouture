<?php
    function controllers_autoload($classname){
        // Reemplaza las barras invertidas por barras normales en el nombre de la clase
        $classname = str_replace("\\", "/", $classname);
        // Construye la ruta completa del archivo de la clase
        $file = __DIR__ . '/' . $classname . '.php';
        // Si el archivo existe, lo requiere
        if (file_exists($file)) require_once $file;
    }

    spl_autoload_register('controllers_autoload');
?>