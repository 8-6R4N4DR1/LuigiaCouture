<?php

    /**
     * Controlador del carrito.
     * 
     * Tiene los siguientes métodos...
     * gestion(): Muestra la vista del carrito.
     * add(): Añade un producto al carrito.
     * delete(): Elimina un producto del carrito.
     * clear(): Vacía el carrito completo.
     * up(): Incrementa en 1 las unidades de un producto en el carrito.
     * down(): Decrementa en 1 las unidades de un producto en el carrito.
     */

    namespace controllers;

    use helpers\Utils;
    use models\Producto;

    class CarritoController {

        /**
         * Método para requerir la vista de la lista del carrito con paginación.
         * Si el usuario introduce una página no válida, se carga la más cercana a ese valor.
         */

        public function gestion() {

            Utils::isIdentity();
            
            $productosPorPagina = ITEMS_PER_PAGE;

            // Aqui iniciamos el numero de página, y abajo redirigimos a 1 o la última página si la página es menor que 1 o mayor que el total de páginas

            $_SESSION['pag'] = isset($_GET['pag']) ? (int)$_GET['pag'] : 1;

            $productos = $_SESSION['carrito'] ?? [];

            $totalPag = max(1, ceil(count($productos) / $productosPorPagina));
            $productos = array_slice($productos, ($_SESSION['pag'] - 1) * $productosPorPagina, $productosPorPagina);

            if($totalPag == 0) $totalPag = 1;

            // Ahora redirigimos a la primera o última página si la página es menor que 1 o mayor que el total de páginas
            
            if($_SESSION['pag'] < 1){
                header("Location:" . BASE_URL . "carrito/gestion&pag=1");
                exit;
            }
            
            if($_SESSION['pag'] > $totalPag){
                header("Location:" . BASE_URL . "carrito/gestion&pag=" . $totalPag);
                exit;
            }

            require_once 'views/carrito/gestion.php';

        }

        /**
         * Método para añadir un número determinado de unidades de un producto al carrito.
         * Se maneja de forma que, si tenemos sesión iniciada, se termine añadiendo el producto
         * correctamente.
         */

        public function add() {
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_SESSION['redirect_after_login'])) {

                $producto_id = isset($_POST['productoId']) ? (int) $_POST['productoId'] : false;
                $cantidad = isset($_POST['cantidad']) ? (int) $_POST['cantidad'] : 1;

                if (!isset($_SESSION['identity'])) {
                
                    $_SESSION['redirect_after_login'] = [
                        'productoId' => $producto_id,
                        'cantidad' => $cantidad,
                    ];
                
                    header('Location: ' . BASE_URL . 'usuario/login');
                    exit;
                
                }else{

                    if (isset($_SESSION['redirect_after_login'])) {

                        $producto_id = $_SESSION['redirect_after_login']['productoId'];
                        $cantidad = $_SESSION['redirect_after_login']['cantidad'];

                        Utils::deleteSession('redirect_after_login');

                    }

                }

                if ($producto_id && $cantidad > 0) {

                    $producto = Producto::getById($producto_id);

                    if ($producto) {

                        $stockDisponible = $producto->getStock();
                        $cantidadEnCarrito = 0;

                        // Verificamos si ya hay unidades de este producto en el carrito
                        if (isset($_SESSION['carrito'])) {

                            foreach ($_SESSION['carrito'] as $indice => $elemento) {

                                if ($elemento['id_producto'] == $producto_id) {

                                    $cantidadEnCarrito = $elemento['unidades'];
                                    break;

                                }

                            }

                        }

                        // Stock dinámico: comprobamos si la suma no supera el stock disponible
                        if ($cantidadEnCarrito + $cantidad <= $stockDisponible) {

                            if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];
                        
                            $encontrado = false;

                            foreach ($_SESSION['carrito'] as $indice => $elemento) {

                                if ($elemento['id_producto'] == $producto_id) {

                                    $_SESSION['carrito'][$indice]['unidades'] += $cantidad;
                                    $encontrado = true;
                                    break;

                                }

                            }

                            if (!$encontrado) {

                                $_SESSION['carrito'][] = [
                                    'id_producto' => $producto_id,
                                    'unidades' => $cantidad
                                ];

                            }

                            $_SESSION['cantidadAnadida'] = $cantidad;
                            $_SESSION['carritoResultado'] = 'complete';

                            // Guardamos la cookie del carrito

                            Utils::saveCookieCarrito();

                        } else {

                            $_SESSION['carritoResultado'] = 'failed_stock';

                        }


                    } else {

                        $_SESSION['carritoResultado'] = 'failed';

                    }

                    Utils::saveCookieCarrito();
                    header('Location: ' . BASE_URL . ($producto_id ? 'producto/ver&id=' . $producto_id . "#carrito" : ''));
                    exit;

                } else {

                    $_SESSION['carritoResultado'] = 'failed';
                    header('Location: ' . BASE_URL);
                    exit;

                }

            } else {

                header('Location: ' . BASE_URL);
                exit;

            }

        }


        /**
         * Método para eliminar una línea en el carrito, que es, de un producto
         * del carrito, todas sus unidades, es decir, eliminar el producto del carrito
         * 
         * Si el carrito se queda vacío, eliminamos la posición correspondiente en la cookie 
         * multiusuario del carrito (Utils).
         */

        public function delete() {

            Utils::isIdentity();

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                
                // Obtener el índice del producto que se quiere eliminar del carrito

                $indice = isset($_GET['index']) ? (int) $_GET['index'] : false;
                
                if ($indice !== false && isset($_SESSION['carrito'][$indice])) {
                    
                    // Eliminar el producto del carrito

                    unset($_SESSION['carrito'][$indice]);

                    $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reindexar el array

                    // Si el carrito está vacío, eliminamos la sesión del carrito

                    if (count($_SESSION['carrito']) == 0) {
                        Utils::deleteSession('carrito');
                        Utils::deleteCookieCarrito();
                    }

                }
        
                Utils::saveCookieCarrito();
                header('Location: ' . BASE_URL . 'carrito/gestion');
                exit;

            } else {

                header('Location: ' . BASE_URL);
                exit;

            }

        }

        /**
         * Método para vaciar el carrito.
         */

        public function clear() {

            Utils::isIdentity();

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {

                Utils::deleteSession('carrito');
                Utils::deleteCookieCarrito();

                header('Location: ' . BASE_URL . 'carrito/gestion');
                exit;

            } else {

                header('Location: ' . BASE_URL);
                exit;

            }

        }

        /**
         * Método para incrementar en 1 las unidades de un producto en el carrito.
         * Controla si se sobrepasa el stock que tiene el producto y avisa de ello.
         * No inserta más unidades si no hay más stock.
         */

    }

?>