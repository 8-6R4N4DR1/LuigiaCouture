<?php
    
    // Búffer de salida para evitar errores de cabecera

    ob_start();

    // Inicio de sesión

    session_start();

    // Importar controlador de error, modelo de usuario y utilidades

    use controllers\ErrorController;
    use helpers\Utils;
    use models\Categoria;
    use models\Producto;
    use models\Usuario;

    // Autoload y configuración

    require_once 'autoload.php';
    require_once 'config.php';
    
    // Cookies de recuerdame y carrito

    Utils::loadRecuerdameCookie();
    Utils::loadCookieCarrito();

    // Verificar si el usuario está en la sesión y actualizar sus datos desde la base de datos

    if (isset($_SESSION['identity']) && isset($_SESSION['identity']['id'])) {

        $usuario = Usuario::getById($_SESSION['identity']['id']);

        if ($usuario) {

            $_SESSION['identity'] = [
                'id' => $usuario->getId(),
                'nombre' => $usuario->getNombre(),
                'apellidos' => $usuario->getApellidos(),
                'email' => $usuario->getEmail(),
                'rol' => $usuario->getRol()
            ];

            if($usuario->getRol() == 'admin') $_SESSION['admin'] = true;

        }
        
    }

    // Genero el titulo de la página dinámicamente

    $titulo = "Luigia Couture";

    if(isset($_GET['controller'])) {

        $controller = $_GET['controller'];
        $action = $_GET['action'] ?? 'index';
        $id = $_GET['id'] ?? null;

        $producto = $id ? Producto::getById($id) : null;
        $categoria = $id ? Categoria::getById($id) : null;
        $usuario = $id ? Usuario::getById($id) : null;

        $nombreProducto = $producto ? $producto->getNombre() : null;
        $nombreCategoria = $categoria ? $categoria->getNombre() : null;
        $nombreUsuario = $usuario ? $usuario->getNombre() : null;

        $titulos = [
            'carrito' => [
                'gestion' => 'Carrito' . (isset($_SESSION['carrito']) ? ' (' . Utils::statsCarrito()['totalCuentas'] . ' producto' . (Utils::statsCarrito()['totalCuentas'] > 1 ? 's' : '') . ')' : ' de compras')
            ],
            'categoria' => [
                'admin' => 'Administrar categorías',
                'crear' => 'Crear categoría',
                'gestion' => 'Gestionar ' . $nombreCategoria
            ],
            'info' => [
                'contacto' => 'Contacto',
                'sobre_nosotros' => 'Sobre nosotros',
                'terminos_condiciones' => 'Términos y condiciones'
            ],
            'pedido' => [
                'admin' => 'Administrar pedidos',
                'crear' => 'Realizar pedido',
                'hecho' => 'Pedido solicitado',
                'misPedidos' => 'Mis pedidos - ' . (isset($_SESSION['identity']) ? $_SESSION['identity']['nombre'] : 'Usuario'),
                'ver' => 'Detalle del pedido (#' . (isset($id) ? $id : '') . ')'
            ],
            'producto' => [
                'admin' => 'Administrar productos',
                'buscar' => 'Búsqueda: ' . (isset($_GET['search']) ? $_GET['search'] : 'Producto') . '',
                'crear' => 'Crear producto',
                'gestion' => 'Gestionar ' . $nombreProducto,
                'recomendados' => 'Luigia Couture',
                'ver' => $nombreProducto
            ],
            'usuario' => [
                'admin' => 'Administrar usuarios',
                'crear' => 'Crear usuario',
                'editar' => 'Editar ' . $nombreUsuario,
                'gestion' => 'Perfil de usuario - ' . (isset($_SESSION['identity']) ? $_SESSION['identity']['nombre'] : 'Usuario'),
                'login' => 'Iniciar sesión',
                'registrarse' => 'Registrarse'
            ]
        ];

        // Asignar el título si existe en el array, si no, generar uno genérico

        if(isset($titulos[$controller][$action])) {

            $titulo = $titulos[$controller][$action];

        } else {

            $titulo = ucfirst($controller) . ' - ' . ucfirst($action);

        }

    }

    // Requerir el header

    require_once 'views/layout/header.php';

    // 1. Si existe el controlador en la URL, se ejecuta ese
    // 2. Si no, se ejecuta el controlador por defecto
    // 3. Si el controlador no existe, se ejecuta el controlador de error

    if (isset($_GET['controller'])) {

        $nombre_controlador = 'controllers\\' . ucfirst($_GET['controller']) . 'Controller';
        
    } elseif (!isset($_GET['controller']) && !isset($_GET['action'])) {

        $nombre_controlador = 'controllers\\' . CONTROLLER_DEFAULT . 'Controller';

    } else { // Realmente este else no haría falta, pero lo dejo por claridad

        (new ErrorController())->index();

    }

    // Compruebo si la clase existe
    
    if (class_exists($nombre_controlador)) {

        $controlador = new $nombre_controlador();

        // 1. Si existe la acción y el método en el controlador, se ejecuta ese
        // 2. Si no, se ejecuta la acción por defecto
        // 3. Si la acción no existe, se ejecuta el controlador de error

        if (isset($_GET['action']) && method_exists($controlador, $_GET['action'])) {

            $action = $_GET['action'];
            $controlador->$action();

        } elseif (!isset($_GET['controller']) && !isset($_GET['action'])) {

            $action_default = ACTION_DEFAULT;
            $controlador->$action_default();

        } else {

            (new ErrorController())->index();

        }

    } else {

        (new ErrorController())->index();

    }

    // Requerir el footer

    require_once 'views/layout/footer.php';

    // Actualizo colores de interfaz en base al campo "color" de la tabla "usuarios"

    

?>