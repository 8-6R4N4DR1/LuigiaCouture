<?php

    /**
     * Modelo de las categorías de los productos
     * 
     * Contiene los métodos...
     * save(): para guardar una nueva categoría en la base de datos
     * update(): para actualizar una categoría existente en la base de datos
     * delete(): para eliminar una categoría de la base de datos
     * getById(): para obtener una categoría de la base de datos por su ID
     * getAll(): para obtener todas las categorías de la base de datos 
     * getLastId(): para obtener el último ID insertado en la tabla de categorías
     */

    namespace models;

    use lib\BaseDatos;

    class Categoria {

        private int $id;
        private string $nombre;

        // Getters y Setters

        public function getId(): int {
            return $this->id;
        }

        public function getNombre(): string {
            return $this->nombre;
        }

        public function setId(int $id): void {
            $this->id = $id;
        }

        public function setNombre(string $nombre): void {
            $this->nombre = $nombre;
        }

        // Métodos dinámicos

        public function save(): bool {

            $baseDatos = new BaseDatos();
            $baseDatos->ejecutar("INSERT INTO categorias VALUES(null, :nombre)", [
                ':nombre' => $this->nombre
            ]);
            $output = $baseDatos->getNumeroRegistros() == 1;
            $baseDatos->cerrarConexion();
            return $output;

        }

        public function update(): bool {

            $baseDatos = new BaseDatos();
            $baseDatos->ejecutar("UPDATE categorias SET nombre = :nombre WHERE id = :id", [
                ':nombre' => $this->nombre,
                ':id' => $this->id
            ]);
            $output = $baseDatos->getNumeroRegistros() == 1;
            $baseDatos->cerrarConexion();
            return $output;

        }

        public function delete(): bool {

            $baseDatos = new BaseDatos();
            $baseDatos->ejecutar("SELECT MAX(id) AS id FROM categorias");
            $maxId = $baseDatos->getNextRegistro();
            $maxId = $maxId ? $maxId['id'] : null;
            $baseDatos->ejecutar("DELETE FROM categorias WHERE id = :id", [
                ':id' => $this->id
            ]);
            $output = $baseDatos->getNumeroRegistros() == 1;
            
            if ($output && $this->id == $maxId) {

                $baseDatos->ejecutar("SELECT MAX(id) AS id FROM categorias");
                $nuevoMaxId = $baseDatos->getNextRegistro();
                $nuevoMaxId = $nuevoMaxId ? $nuevoMaxId['id'] : 0; // Si no hay más registros, el nuevo máximo es 0
                $nuevoAutoIncrement = $nuevoMaxId + 1; // Si la tabla está vacía, el próximo ID empieza en 1
                $baseDatos->ejecutar("ALTER TABLE categorias AUTO_INCREMENT = $nuevoAutoIncrement");

            }

            $baseDatos->cerrarConexion();
            return $output;

        }

        // Métodos estáticos

        public static function getById(int $id): ?Categoria {

            $baseDatos = new BaseDatos();
            $baseDatos->ejecutar("SELECT * FROM categorias WHERE id = :id", [
                ':id' => $id
            ]);

            if ($baseDatos->getNextRegistro() == 1) {

                $registro = $baseDatos->getNextRegistro();
                $categoria = new Categoria();
                $categoria->setId($registro['id']);
                $categoria->setNombre($registro['nombre']);
                $baseDatos->cerrarConexion();
                return $categoria;

            }

            $baseDatos->cerrarConexion();
            return null;

        }

        public static function getAll(): array {

            $baseDatos = new BaseDatos();
            $baseDatos->ejecutar("SELECT * FROM categorias");
            $registros = $baseDatos->getRegistros();
            $categorias = [];

            foreach ($registros as $registro) {

                $categoria = new Categoria();
                $categoria->setId($registro['id']);
                $categoria->setNombre($registro['nombre']);
                array_push($categorias, $categoria);

            }

            $baseDatos->cerrarConexion();
            return $categorias;

        }

        /* Método auxiliar para obtener el último ID insertado en la tabla de categorías,
        lo hubiera implementado antes si hubiera sabido que era necesario */

        public static function getLastId(): int {

            $baseDatos = new BaseDatos();
            $baseDatos->ejecutar("SELECT MAX(id) AS id FROM categorias");
            $registro = $baseDatos->getNextRegistro();
            $id = $registro['id'];
            $baseDatos->cerrarConexion();
            return $id;

        }

    }

?>