<?php

    /**
     * Modelo de los productos
     * 
     * Contiene los métodos...
     * save(): para guardar un nuevo producto en la base de datos
     * update(): para actualizar un producto existente en la base de datos
     * delete(): para eliminar un producto de la base de datos
     * getById(): para obtener un producto por su ID
     * getByCategoria(): para obtener un array de productos por su id de categoría
     * getByQuery(): para obtener un array de productos por su nombre o categoría
     * getAll(): para obtener un array de todos los productos
     */

    namespace models;

    use lib\BaseDatos;

    class Producto {

        private int $id;
        private int $categoriaId;
        private string $nombre;
        private string $descripcion;
        private float $precio;
        private int $stock;
        private string $oferta;
        private string $fecha;
        private string $imagen;

        // Getters y Setters

        public function getId(): int{
            return $this->id;
        }

        public function getCategoriaId(): int{
            return $this->categoriaId;
        }

        public function getNombre(): string{
            return $this->nombre;
        }

        public function getDescripcion(): string{
            return $this->descripcion;
        }

        public function getPrecio(): float{
            return $this->precio;
        }

        public function getStock(): int{
            return $this->stock;
        }

        public function getOferta(): string{
            return $this->oferta;
        }

        public function getFecha(): string{
            return $this->fecha;
        }

        public function getImagen(): string{
            return $this->imagen;
        }

        public function setId(int $id): void{
            $this->id = $id;
        }

        public function setCategoriaId(int $categoriaId): void{
            $this->categoriaId = $categoriaId;
        }

        public function setNombre(string $nombre): void{
            $this->nombre = $nombre;
        }

        public function setDescripcion(string $descripcion): void{
            $this->descripcion = $descripcion;
        }

        public function setPrecio(float $precio): void{
            $this->precio = $precio;
        }

        public function setStock(int $stock): void{
            $this->stock = $stock;
        }

        public function setOferta(string $oferta): void{
            $this->oferta = $oferta;
        }

        public function setFecha(string $fecha): void{
            $this->fecha = $fecha;
        }

        public function setImagen(string $imagen): void{
            $this->imagen = $imagen;
        }

        // Métodos dinámicos

        public function save(): bool {

            $baseDatos = new BaseDatos();

            $baseDatos->ejecutar("INSERT INTO productos VALUES(null, :categoria_id, :nombre, :descripcion, :precio, :stock, :oferta, :fecha, null)", [
                ':categoria_id' => $this->categoriaId,
                ':nombre' => $this->nombre,
                ':descripcion' => $this->descripcion,
                ':precio' => $this->precio,
                ':stock' => $this->stock,
                ':oferta' => $this->oferta,
                ':fecha' => $this->fecha
            ]);
        
            if ($baseDatos->getNumeroRegistros() == 1) $this->setId($baseDatos->getLastId());
        
            $output = $baseDatos->getNumeroRegistros() == 1;
            $baseDatos->cerrarConexion();
            return $output;
            
        }
        

        public function update(): bool {

            $baseDatos = new BaseDatos();

            if (!isset($this->imagen)) {

                $baseDatos->ejecutar("SELECT imagen FROM productos WHERE id = :id", [
                    ':id' => $this->id
                ]);
        
                $registro = $baseDatos->getNextRegistro();
                $this->imagen = $registro['imagen'];

            }

            $baseDatos->ejecutar("UPDATE productos SET categoria_id = :categoria_id, nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock, oferta = :oferta, fecha = :fecha, imagen = :imagen WHERE id = :id", [
                ':categoria_id' => $this->categoriaId,
                ':nombre' => $this->nombre,
                ':descripcion' => $this->descripcion,
                ':precio' => $this->precio,
                ':stock' => $this->stock,
                ':oferta' => $this->oferta,
                ':fecha' => $this->fecha,
                ':imagen' => $this->imagen,
                ':id' => $this->id
            ]);
            
            $output = $baseDatos->getNumeroRegistros() == 1;
            $baseDatos->cerrarConexion();
            return $output;

        }

        public function delete(): bool {

            $baseDatos = new BaseDatos();
            $baseDatos->ejecutar("SELECT MAX(id) AS id FROM productos");
            $maxId = $baseDatos->getNextRegistro();
            $maxId = $maxId ? $maxId['id'] : null;

            $baseDatos->ejecutar("DELETE FROM productos WHERE id = :id", [
                ':id' => $this->id
            ]);

            $output = $baseDatos->getNumeroRegistros() == 1;

            if ($output && $this->id == $maxId) {
                
                $baseDatos->ejecutar("SELECT MAX(id) AS id FROM productos");

                $nuevoMaxId = $baseDatos->getNextRegistro();
                $nuevoMaxId = $nuevoMaxId ? $nuevoMaxId['id'] : 0;

                $nuevoAutoIncrement = $nuevoMaxId + 1; // Si la tabla está vacía, empieza en 1

                $baseDatos->ejecutar("ALTER TABLE productos AUTO_INCREMENT = $nuevoAutoIncrement");

            }

            $baseDatos->cerrarConexion();
            return $output;
            
        }

        // Métodos estáticos

        public static function getById(int $id): ?Producto {

            $baseDatos = new BaseDatos();

            $baseDatos->ejecutar("SELECT * FROM productos WHERE id = :id", [
                ':id' => $id
            ]);
        
            if ($baseDatos->getNumeroRegistros() == 1) {
        
                $registro = $baseDatos->getNextRegistro();
        
                $producto = new Producto();
        
                $producto->setId($registro['id']);
                $producto->setCategoriaId($registro['categoria_id']);
                $producto->setNombre($registro['nombre']);
                $producto->setDescripcion($registro['descripcion']);
                $producto->setPrecio($registro['precio']);
                $producto->setStock($registro['stock']);
                $producto->setOferta($registro['oferta']);
                $producto->setFecha($registro['fecha']);
                $producto->setImagen($registro['imagen']);
        
                $baseDatos->cerrarConexion();
                return $producto;
        
            }

            $baseDatos->cerrarConexion();
            return null;

        }

        public static function getByCategoria(int $categoriaId): array {

            $baseDatos = new BaseDatos();

            $baseDatos->ejecutar("SELECT * FROM productos WHERE categoria_id = :categoria_id", [
                ':categoria_id' => $categoriaId
            ]);
        
            $registros = $baseDatos->getRegistros();
            $productos = [];
        
            foreach ($registros as $registro) {
        
                $producto = new Producto();
        
                $producto->setId($registro['id']);
                $producto->setCategoriaId($registro['categoria_id']);
                $producto->setNombre($registro['nombre']);
                $producto->setDescripcion($registro['descripcion']);
                $producto->setPrecio($registro['precio']);
                $producto->setStock($registro['stock']);
                $producto->setOferta($registro['oferta']);
                $producto->setFecha($registro['fecha']);
                $producto->setImagen($registro['imagen']);
        
                array_push($productos, $producto);
                
            }

            $baseDatos->cerrarConexion();
            return $productos;

        }

        // El siguiente método busca productos que coincidan con la query dada
        // ya sea en su nombre de producto o en el nombre de su categoría

        public static function getByQuery(string $query): array {

            $baseDatos = new BaseDatos();

            $baseDatos->ejecutar("SELECT p.* FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.nombre LIKE :query OR c.nombre LIKE :query", [
                ':query' => "%$query%"
            ]);
        
            $registros = $baseDatos->getRegistros();
            $productos = [];
        
            foreach ($registros as $registro) {
        
                $producto = new Producto();
        
                $producto->setId($registro['id']);
                $producto->setCategoriaId($registro['categoria_id']);
                $producto->setNombre($registro['nombre']);
                $producto->setDescripcion($registro['descripcion']);
                $producto->setPrecio($registro['precio']);
                $producto->setStock($registro['stock']);
                $producto->setOferta($registro['oferta']);
                $producto->setFecha($registro['fecha']);
                $producto->setImagen($registro['imagen']);
        
                array_push($productos, $producto);
                
            }

            $baseDatos->cerrarConexion();
            return $productos;

        }

        public static function getAll(): array{

            $baseDatos = new BaseDatos();
            $baseDatos->ejecutar("SELECT * FROM productos");
            $registros = $baseDatos->getRegistros();
            $productos = [];
        
            foreach ($registros as $registro) {

                $producto = new Producto();

                $producto->setId($registro['id']);
                $producto->setCategoriaId($registro['categoria_id']);
                $producto->setNombre($registro['nombre']);
                $producto->setDescripcion($registro['descripcion']);
                $producto->setPrecio($registro['precio']);
                $producto->setStock($registro['stock']);
                $producto->setOferta($registro['oferta']);
                $producto->setFecha($registro['fecha']);
                $producto->setImagen($registro['imagen']);
        
                array_push($productos, $producto);
                
            }

            $baseDatos->cerrarConexion();
            return $productos;

        }


    }