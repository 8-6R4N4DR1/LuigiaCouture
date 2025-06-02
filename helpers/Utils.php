<?php
    namespace helpers;

    class Utils{
        public static function deleteSession($name){
            // Verifica si la sesión existe
            if(isset($_SESSION[$name])){
                // Establece la sesión a null y la elimina
                $_SESSION[$name] = null;
                unset($_SESSION[$name]);
            }
        }

        public static function cifrarPassword($password){
            // Cifra la contraseña con BCRYPT y un coste de 4
            return password_hash($password, PASSWORD_BCRYPT, ['cost' => 4]);
        }

        public static function isNotIdentity(){
            // Verifica si la sesión de identidad no está establecida
            if(!isset($_SESSION['identity'])){
                // Redirige a la página principal
                header('Location:'.BASE_URL);
                exit();
            }
        }

        public static function isIdentity(){
            // Verifica si la sesión de identidad está establecida
            if(isset($_SESSION['identity'])){
                // Redirige a la página principal
                header('Location:'.BASE_URL);
                exit();
            }
        }

        public static function isAdmin(){
            // Verifica si la sesión de identidad no está establecida o si el rol no es 'admin'
            if(!isset($_SESSION['identity']) || $_SESSION['identity']['rol'] !== 'admin'){
                // Redirige a la página principal
                header('Location:'.BASE_URL);
                exit();
            }
        }
    }
?>