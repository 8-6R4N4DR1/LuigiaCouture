<?php

    /**
     * Controlador de las categorías de los productos.
     * 
     * Contiene los métodos:
     * admin():     Requiere la vista de administración de categorías.
     * crear():     Requiere la vista de creación de categorías.
     * guardar():   Valida y guarda una categoría en la base de datos.
     * editar():    Valida y edita una categoría en la base de datos.
     * gestion():   Requiere la vista de edición de categorías.
     * eliminar():  Elimina una categoría de la base de datos.
     */

    namespace controllers;

    use models\Categoria;
    use models\Producto;
    use models\Usuario;
    use models\Valoracion;
    use models\LineaPedido;
    use models\Pedido;
    use helpers\Utils;

?>