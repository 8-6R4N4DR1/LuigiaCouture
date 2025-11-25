<?php

    /**
     * Clase para la conexión de la base de datos
     * 
     * Métodos:
     * __construct(): Constructor de la clase BaseDatos
     * ejecutar(): Ejecuta una consulta SQL en la base de datos
     * getNextRegistro(): Obtiene el siguiente registro de un conjunto de resultados
     * getRegistros(): Obtiene todos los registros de un conjunto de resultados
     * getNumeroRegistros(): Obtiene el número de registros de un conjunto de resultados
     * getLastId(): Obtiene el último ID insertado en la base de datos
     * cerrarConexion(): Cierra la conexión a la base de datos
     * __destruct(): Destructor de la clase BaseDatos
     */

    namespace lib;

    use PDO;
    use PDOException;
    use PDOStatement;

    class BaseDatos {

        private string $servidor;
        private string $usuario;
        private string $password;
        private string $name;

        private ?PDO $conexion;
        private ?PDOStatement $consulta;

        // Constructor de la clase BaseDatos
        public function __construct() {
            try{

                $this->servidor = DB_HOST;
                $this->usuario = DB_USER;
                $this->password = DB_PASSWORD;
                $this->name = DB_NAME;

                $this->conexion = new PDO("mysql:host=$this->servidor;dbname=$this->name;charset=utf8mb4", $this->usuario, $this->password);

                $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            }catch(PDOException $e){

                echo "<h1>Error de conexión: " . $e->getMessage() . "</h1>";

            }

        }

        // Ejecuta una consulta SQL en la base de datos con parámetros opcionales para evitar inyecciones SQL
        public function ejecutar(string $sql, array $parametros = []): void{

            try{

                $this->consulta = $this->conexion->prepare($sql);
                $this->consulta->execute($parametros);

            }catch(PDOException $e){

                echo "<h1>Error al ejecutar la consulta: " . $e->getMessage() . "</h1>";

            }

        }

        // Obtener registros de la base de datos

        public function getNextRegistro(): ?array{

            return $this->consulta->fetch();

        }

        public function getRegistros(): array{

            return $this->consulta->fetchAll();

        }

        public function getNumeroRegistros(): int{

            return $this->consulta->rowCount();

        }

        public function getLastId(): int{

            return $this->conexion->lastInsertId();

        }

        // Cerrar la conexión a la base de datos

        public function cerrarConexion(): void{

            $this->conexion = null;
            $this->consulta = null;

        }

        public function __destruct() {

            $this->cerrarConexion();

        }

    }
    
?>