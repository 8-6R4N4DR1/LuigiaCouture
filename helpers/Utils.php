<?php
    namespace helpers;

    use lib\BaseDatos;
    use models\Usuario;

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

        public static function statsCarrito(){
            $stats = array(
                'count' => 0, // Contador de productos en el carrito
                'totalCount' => 0, // Total de productos en el carrito
                'total' => 0 // Total del carrito
            );

            if(isset($_SESSION['carrito'])){
                $stats['count'] = count($_SESSION['carrito']); // Cuenta los productos en el carrito
                foreach($_SESSION['carrito'] as $index => $producto){
                    // Suma el total de cada producto en el carrito
                    $stats['totalCount'] += $producto['unidades'];

                    $prod = Producto::getProductoPorId($producto['id_producto']); // Obtiene el producto por su ID
                    $precioTotal = $prod->getPrecio() * (1 - $prod->getOferta() / 100) * $producto['unidades'];
                    $stats['total'] += $precioTotal; // Suma el total del producto al total del carrito
                }
            }
            return $stats; // Devuelve las estadísticas del carrito
        }

        public static function saveCookieCarrito(){
            if(isset($_SESSION['carrito']) && isset($_SESSION['identity'])){
                $carritos = [];
                if(isset($_COOKIE['carrito'])){
                    // Decodifica el carrito de la cookie si existe
                    $carritos = json_decode($_COOKIE['carrito'], true);
                }
                $email = $_SESSION['identity']['email']; // Obtiene el email del usuario de la sesión
                $carritos[$email] = $_SESSION['carrito']; // Guarda el carrito en el array con el email como clave
                setcookie('carrito', json_encode($carritos), time() + 60*60*24*3, '/'); // Guarda el carrito en una cookie que expira en 3 días
            }
        }

        public static function loadCookieCarrito(){
            if(isset($_COOKIE['carrito']) && isset($_SESSION['identity'])){
                $carritos = json_decode($_COOKIE['carrito'], true); // Decodifica el carrito de la cookie
                $email = $_SESSION['identity']['email']; // Obtiene el email del usuario de la sesión
                if(isset($carritos[$email])){
                    $_SESSION['carrito'] = $carritos[$email]; // Carga el carrito del usuario en la sesión
                }
            }
        }

        public static function deleteCookieCarrito(){
            if(isset($_COOKIE['carrito']) && isset($_SESSION['identity'])){
                $carritos = json_decode($_COOKIE['carrito'], true); // Decodifica el carrito de la cookie
                $email = $_SESSION['identity']['email']; // Obtiene el email del usuario de la sesión
                if(isset($carritos[$email])){
                    unset($carritos[$email]); // Elimina el carrito del usuario del array
                }

                if(!empty($carritos)){
                    setcookie('carrito', json_encode($carritos), time() + 60*60*24*3, '/'); // Guarda el carrito actualizado en la cookie
                } else {
                    setcookie('carrito', '', time() - 3600, '/'); // Elimina la cookie si no hay carritos
                }
            }
        }

        public static function deleteCookieCarritoByEmail(string $email){
            if(isset($_COOKIE['carrito'])){
                $carritos = json_decode($_COOKIE['carrito'], true); // Decodifica el carrito de la cookie
                if(isset($carritos[$email])){
                    unset($carritos[$email]); // Elimina el carrito del usuario del array
                }

                if(!empty($carritos)){
                    setcookie('carrito', json_encode($carritos), time() + 60*60*24*3, '/'); // Guarda el carrito actualizado en la cookie
                } else {
                    setcookie('carrito', '', time() - 3600, '/'); // Elimina la cookie si no hay carritos
                }
            }
        }

        public static function loadRememberCookie(){
            if(isset($_COOKIE['recuerdame'])){
                $email = $_COOKIE['recuerdame']; // Obtiene el email de la cookie
                $usuario = Usuario::getUserPorEmail($email); // Obtiene el usuario por su email
                if($usuario){
                    $_SESSION['identity'] = [
                        'id' => $usuario->getId(),
                        'nombre' => $usuario->getNombre(),
                        'apellidos' => $usuario->getApellidos(),
                        'email' => $usuario->getEmail(),
                        'redesSociales' => $usuario->getRedesSociales(),
                        'rol' => $usuario->getRol()
                    ];
                    if($usuario->getRol() == 'admin'){
                        $_SESSION['admin'] = true; // Establece la sesión de administrador si el usuario es admin
                    }
                }
            }
        }

        public static function usuarioValoraProducto(int $userId, int $productoId){
            $bd = new BaseDatos();
            $bd->ejecutarConsulta("SELECT *
                                  FROM lineas_pedido lp
                                  INNER JOIN pedidos p ON lp.id_pedido = p.id
                                  WHERE p.id_usuario = :id_usuario
                                  AND lp.id_producto = :id_producto
                                  AND p.estado = 'enviado'
                                  LIMIT 1", [
                                    ':id_usuario' => $userId,
                                    ':id_producto' => $productoId
                                  ]);
            
            if($bd->getNumRegistros() == 0){
                $bd->closeBD();
                return false; // El usuario no ha comprado el producto
            }

            $bd->ejecutarConsulta(
                "SELECT *
                FROM valoraciones
                WHERE id_usuario = :id_usuario
                AND id_producto = :id_producto
                LIMIT 1", [
                    ':id_usuario' => $userId,
                    ':id_producto' => $productoId
                ]
            );

            $salida = $bd->getNextRegistro() == 0;

            $bd->closeBD();

            return $salida; // Devuelve true si el usuario no ha valorado el producto, false si ya lo ha hecho
        }

        public static function variasComprasDeUnProducto(int $productoId, int $userId, int $pedidoExcluidoId){
            $bd = new BaseDatos();

            $bd->ejecutarConsulta(
                "SELECT COUNT(*) AS total
                FROM lineas_pedido lp
                JOIN pedidos p ON lp.id_pedido = p.id
                WHERE lp.id_producto = :id_producto
                AND p.id_usuario = :id_usuario
                AND p.id != :id_pedido",
                [
                    ':id_producto' => $productoId,
                    ':id_usuario' => $userId,
                    ':id_pedido' => $pedidoExcluidoId
                ]
            );

            $registro = $bd->getNextRegistro();
            $hayVariasCompras = ($registro['total'] > 0);
            $bd->closeBD();
            return $hayVariasCompras; // Devuelve true si hay varias compras del producto por parte del usuario, false si no
        }


    }
?>