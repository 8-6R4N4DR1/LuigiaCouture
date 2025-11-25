<?php

    /**
     * Funciones de utilidad varias
     * 
     * deleteSession(): elimina una variable $_SESSION si existe
     * isAdmin(): comprueba si el usuario es administrador
     * isIdentity(): comprueba si el usuario está identificado
     * statsCarrito(): devuelve las estadísticas del carrito
     * saveCookieCarrito(): guarda las estadísticas del carrito en una cookie
     * loadCookieCarrito(): carga las estadísticas del carrito desde una cookie
     * deleteCookieCarrito(): elimina la cookie del carrito
     * deleteCookieCarritoByEmail(): elimina la cookie del carrito asociada al email de un usuario en específico
     * loadRecuerdameCookie(): carga la cookie de "recuerdame" para el login automático
     * usuarioValorarProducto(): comprueba si un usuario puede valorar un producto
     * existMasComprasProducto(): comprueba si existen más compras de un producto por parte de un usuario
     * enviarCorreo(): envía un correo electrónico utilizando PHPMailer
     */

    namespace helpers;

    use lib\BaseDatos;
    use models\Producto;
    use models\Usuario;

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    class Utils {
        // Aquí irían las implementaciones de las funciones mencionadas en el comentario

        /**
         * Elimina una variable de sesión si existe
         * 
         * @param string $name Nombre de la variable de sesión a eliminar
         */

        public static function deleteSession(string $name): void {

            if (isset($_SESSION[$name])) {

                $_SESSION[$name] = null;
                unset($_SESSION[$name]);

            }

        }

        /**
         * Comprueba si el usuario es administrador
         * Si no lo es, redirige a la página principal
         */

        public static function isAdmin(): void {

            if(!isset($_SESSION['identity']) || $_SESSION['identity']['rol'] !== 'admin'){

                header('Location:'.BASE_URL);
                exit;

            }

        }

        /**
         * Comprueba si el usuario está identificado
         * Si no lo está, redirige a la página principal
         */

        public static function isIdentity():void {

            if(!isset($_SESSION['identity'])){

                header('Location:'.BASE_URL);
                exit;

            }
            
        }

        /**
         * Devuelve las estadísticas del carrito
         * 
         * @return array Array con las estadísticas del carrito: 'cuenta', 'totalCuentas', 'total'
         */

        public static function statsCarrito(): array {

            $stats = array(
                'cuenta' => 0,
                'totalCuentas' => 0,
                'total' => 0
            );

            if(isset($_SESSION['carrito'])){

                $stats['cuenta'] = count($_SESSION['carrito']);

                foreach($_SESSION['carrito'] as $indice => $producto){

                    $stats['totalCuentas'] += $producto['unidades'];

                    $prod = Producto::getById($producto['id_producto']); // Obtiene la información del producto
                    $precioTotal = $prod->getPrecio() * (1 - $prod->getOferta() / 100) * $producto['unidades'];
                    $stats['total'] += $precioTotal;

                }

            }

            return $stats;

        }

        /**
         * Guarda las estadísticas del carrito en una cookie de usuario actual
         */
        public static function saveCookieCarrito(): void {

            if(isset($_SESSION['carrito']) && isset($_SESSION['identity'])){

                $carritos = [];

                if(isset($_COOKIE['carrito'])){

                    $carritos = json_decode($_COOKIE['carrito'], true);

                }

                $email = $_SESSION['identity']['email'];

                $carritos[$email] = $_SESSION['carrito'];

                setcookie('carrito', json_encode($carritos), time() + 60*60*24*3, '/');

            }

        }

        /**
         * Carga las estadísticas del carrito desde una cookie del usuario actual
         */

        public static function loadCookieCarrito(): void {

            if(isset($_COOKIE['carrito']) && isset($_SESSION['identity'])){

                $carritos = json_decode($_COOKIE['carrito'], true);

                $email = $_SESSION['identity']['email'];

                if(isset($carritos[$email])){

                    $_SESSION['carrito'] = $carritos[$email];

                }

            }

        }

        /**
         * Elimina la cookie del carrito asociada al usuario actual
         */

        public static function deleteCookieCarrito(): void {
            
            if(isset($_COOKIE['carrito']) && isset($_SESSION['identity'])){

                $carritos = json_decode($_COOKIE['carrito'], true);

                $email = $_SESSION['identity']['email'];

                if(isset($carritos[$email])) unset($carritos[$email]);

                if(!empty($carritos)){

                    setcookie('carrito', json_encode($carritos), time() + 60*60*24*3, '/');

                }else{

                    setcookie('carrito', '', time() - 3600, '/');

                }

            }

        }

        /**
         * Elimina la cookie del carrito asociada a un correo electrónico específico
         * Se usa al eliminar un usuario fuera de su sesión actual (administrador)
         */

        public static function deleteCookieCarritoByEmail(string $email): void {
            
            if(isset($_COOKIE['carrito'])){

                $carritos = json_decode($_COOKIE['carrito'], true);

                if(isset($carritos[$email])) unset($carritos[$email]);

                if(!empty($carritos)){

                    setcookie('carrito', json_encode($carritos), time() + 60*60*24*3, '/');

                }else{

                    setcookie('carrito', '', time() - 3600, '/');

                }

            }

        }

        /**
         * Carga la cookie de "recuerdame" para el login automático
         */

        public static function loadRecuerdameCookie(): void {

            if(isset($_COOKIE['recuerdame'])){

                $email = $_COOKIE['recuerdame'];

                $usuario = Usuario::getByEmail($email);

                if($usuario){

                    $_SESSION['identity'] = [
                        'id' => $usuario->getId(),
                        'nombre' => $usuario->getNombre(),
                        'apellidos' => $usuario->getApellidos(),
                        'email' => $usuario->getEmail(),
                        'rol' => $usuario->getRol(),
                        'color' => $usuario->getColor(),
                        'imagen' => $usuario->getImagen()
                    ];

                    if ($usuario->getRol() == 'admin') $_SESSION['admin'] = true;

                }

            }

        }

        /**
         * Comprueba si un usuario puede valorar un producto
         * 
         * @param int $usuarioId ID del usuario
         * @param int $productoId ID del producto
         * @return bool Devuelve true si el usuario puede valorar el producto, false en caso contrario
         */

        public static function usuarioValorarProducto(int $usuarioId, int $productoId): bool {

            // Se tienen que dar dos condiciones para que el usuario pueda valorar el producto:

            $baseDatos = new BaseDatos();

            // 1. El usuario ha comprado el producto al menos una vez y el pedido está enviado

            $baseDatos->ejecutar("SELECT *
                                  FROM lineas_pedidos lp
                                  INNER JOIN pedidos p ON lp.pedido_id = p.id
                                  WHERE p.usuario_id = :usuario_id
                                  AND lp.producto_id = :producto_id
                                  AND p.estado = 'Enviado'
                                  LIMIT 1", [
                ':usuario_id' => $usuarioId,
                ':producto_id' => $productoId
            ]);

            if ($baseDatos->getNumeroRegistros() == 0) {

                $baseDatos->cerrarConexion();
                return false;

            }

            // 2. El usuario no ha valorado el producto

            $baseDatos->ejecutar("SELECT *
                                  FROM valoraciones
                                  WHERE usuario_id = :usuario_id
                                  AND producto_id = :producto_id
                                  LIMIT 1", [
                ':usuario_id' => $usuarioId,
                ':producto_id' => $productoId
            ]);

            $output = $baseDatos->getNumeroRegistros() == 0;

            $baseDatos->cerrarConexion();

            return $output;

        }

        /**
         * Comprueba si existen más compras de un producto por parte de un usuario
         * 
         * @param int $productoId ID del producto
         * @param int $usuarioId ID del usuario
         * @param int $pedidoExcluirId ID del pedido a excluir de la comprobación
         * @return bool Devuelve true si existen más compras del producto por parte del usuario, false en caso contrario
         */

        public static function existMasComprasProducto(int $productoId, int $usuarioId, int $pedidoExcluirId): bool {

            $baseDatos = new BaseDatos();
        
            $baseDatos->ejecutar("SELECT COUNT(*) as total 
                                  FROM lineas_pedidos lp
                                  JOIN pedidos p ON lp.pedido_id = p.id
                                  WHERE lp.producto_id = :producto_id
                                  AND p.usuario_id = :usuario_id
                                  AND p.id != :pedido_id", [
                ':producto_id' => $productoId,
                ':usuario_id' => $usuarioId,
                ':pedido_id' => $pedidoExcluirId
            ]);
    
            $registro = $baseDatos->getSiguienteRegistro();
            
            $hayMasCompras = ($registro['total'] > 0);
        
            $baseDatos->cerrarConexion();
        
            return $hayMasCompras;

        }

        /**
         * Envía un correo electrónico por SMTP utilizando la librería de PHPMailer
         * 
         * Acepta:
         * @param Usuario $usuario Objeto Usuario que recibirá el correo
         * @param string $asunto Asunto del correo
         * @param string $html Ruta al archivo HTML que contiene el cuerpo del correo
         * @param array $variables Array asociativo de variables para reemplazar en el cuerpo del correo
         * @param array $imagenes Array de imágenes para incrustar en el correo (cada imagen es un array con 'ruta', 'cid' y 'nombre')
         * Incluye por defecto las variables {{BASE_URL}} y {{ANIO}} actual en el cuerpo del correo
         * Si ocurre un error al enviar el correo, se registra en un archivo de log para su posterior revisión
         */

        public static function enviarCorreo(Usuario $usuario, string $asunto, string $html, array $variables = [], array $imagenes = []): void{

            $mail = new PHPMailer(true);

            try {

                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = MAIL_USERNAME;
                $mail->Password   = MAIL_PASSWORD;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom(MAIL_USERNAME, mb_encode_mimeheader('Tienda de Señales de Tráfico', 'UTF-8'));
                $mail->addAddress($usuario->getEmail(), $usuario->getNombre());  // Destinatario

                $mail->Subject = $asunto;
                $mail->isHTML(true);

                $body = file_get_contents($html);

                $body = str_replace('{{BASE_URL}}', BASE_URL, $body);
                $body = str_replace('{{ANIO}}', date('Y'), $body);

                foreach($variables as $variable => $valor){
                    $body = str_replace('{{' . $variable . '}}', htmlspecialchars($valor), $body);
                }

                foreach($imagenes as $imagen){
                    $mail->addEmbeddedImage($imagen['ruta'], $imagen['cid'], $imagen['nombre']);
                }
                
                $mail->Body = $body;

                $mail->send();

            } catch (Exception $e) {

                echo "Error al enviar el correo: {$mail->ErrorInfo}";
                
                $ruta = __DIR__ . '/../logs/error.log';
            
                if(!file_exists(dirname($ruta))) mkdir(dirname($ruta), 0777, true);
            
                $contenido = date('Y-m-d H:i:s') . ' - Error al enviar el correo: ' . $mail->ErrorInfo . ' | Exception: ' . $e->getMessage() . "\n";
            
                file_put_contents($ruta, $contenido, FILE_APPEND | LOCK_EX);

            }            

        }

    }

?>