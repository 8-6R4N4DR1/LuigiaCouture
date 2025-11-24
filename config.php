<?php
    // Configuración de la base de datos
    define('DB_HOST', 'localhost'); // Host de la base de datos (con composer sería $_ENV['DB_HOST'])
    define('DB_USER', 'root'); // Usuario de la base de datos (con composer sería $_ENV['DB_USER'])
    define('DB_PASS', ''); // Contraseña de la base de datos (con composer sería $_ENV['DB_PASS'])
    define('DB_NAME', 'tienda'); // Nombre de la base de datos (con composer sería $_ENV['DB_NAME'])

    // Configuración del controlador y acción por defecto
    define('CONTROLLER_DEFAULT', 'Producto'); // Controlador por defecto - Página por defecto
    define('ACTION_DEFAULT', 'destacados'); // Acción de la página o controlador por defecto

    // URL base del proyecto
    define('BASE_URL', 'http://localhost/dashboard/DWES_ProyectoFinal_Tienda_MartinVazquez_Adrian/');

    // Paginación de usuarios, categorías y productos
    define('USERS_PER_PAGE', 5); // Número de usuarios por página
    define('CATEGORIES_PER_PAGE', 3); // Número de categorías por página
    define('PRODUCTS_PER_PAGE', 6); // Número de productos por página
?>