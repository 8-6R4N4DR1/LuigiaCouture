<?php
    namespace lib;

    use PDO;
    use PDOException;
    use PDOStatement;

    class BaseDatos{
        private string $server;
        private string $user;
        private string $password;
        private string $nombre;
        private ?PDO $conexion;
        private ?PDOStatement $consulta;

        public function __construct(){
            try{
                // Configuración de la base de datos
                $this->server = DB_HOST;
                $this->user = DB_USER;
                $this->password = DB_PASS;
                $this->nombre = DB_NAME;

                // Conexión a la base de datos
                $this->conexion = new PDO("mysql:host=$this->server;dbname=$this->nombre;charset=utf8mb4", $this->user, $this->password);
                $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "<h1>Error al conectar a la base de datos: " . $e->getMessage() . "</h1>";
            }
        }

        public function ejecutarConsulta(string $sql, array $parametros = []){
            try{
                // Prepara y ejecuta la consulta SQL
                $this->consulta = $this->conexion->prepare($sql);
                $this->consulta->execute($parametros);
            }catch(PDOException $e){
                // Muestra el mensaje de error por defecto en caso de fallo en la conexión
                echo "<h1>Error en la ejecuci&oacute;n de la consulta: " . $e->getMessage() . "</h1>";
            }
        }

        public function getNextRegistro(): ?array{
            return $this->consulta->fetch();
        }

        public function getRegistros(){
            return $this->consulta->fetchAll();
        }

        public function getNumRegistros(){
            return $this->consulta->rowCount();
        }

        public function getUltimoId(): int{
            return $this->conexion->lastInsertId();
        }

        public function closeBD(){
            $this->conexion = null;
            $this->consulta = null;
        }

        public function __destruct(){
            $this->closeBD();
        }
    }