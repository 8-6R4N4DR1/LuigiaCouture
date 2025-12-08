<?php

    /**
     * Archivo de configuraciones de variables globales de la aplicación.
     * Se definen las variables de entorno y se definen constantes para su uso en toda la aplicación.
     */

    // Dotenv para cargar variables de entorno desde el archivo .env con Composer
    require_once 'vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Configuración de la base de datos

    define('DB_HOST', $_ENV['DB_HOST']);
    define('DB_NAME', $_ENV['DB_NAME']);
    define('DB_USER', $_ENV['DB_USER']);
    define('DB_PASSWORD', $_ENV['DB_PASSWORD']);

    // Controlador y acción por defecto

    define('CONTROLLER_DEFAULT', 'Producto');
    define('ACTION_DEFAULT', 'inicio');

    // URL Base

    define('BASE_URL', 'http://localhost/dashboard/LuigiaCoutureTFG/');

    // Paginación

    define('ITEMS_PER_PAGE', 10);
    define('PRODUCTS_PER_PAGE', 12); // Conviene ser múltiplo de 3 para una mejor visualización en la cuadrícula

    // SMTP de Gmail para envío de correos

    define('MAIL_USERNAME', $_ENV['MAIL_USERNAME']);
    define('MAIL_PASSWORD', $_ENV['MAIL_PASSWORD']);

    // Paypal API Credentials

    define('PAYPAL_CLIENT_ID', $_ENV['PAYPAL_CLIENT_ID']);
    define('PAYPAL_SECRET', $_ENV['PAYPAL_SECRET']);
    
?>