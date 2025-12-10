<?php 

    /**
     * Modelo de los pedidos de los usuarios
     * 
     * Contiene los métodos...
     * save(): para guardar un nuevo pedido en la base de datos
     * update(): para actualizar un pedido existente en la base de datos
     * delete(): para eliminar un pedido de la base de datos
     * getById(): para obtener un pedido de la base de datos por su ID
     * getByUsuario(): para obtener todos los pedidos de un usuario
     * getAll(): para obtener todos los pedidos de la base de datos 
     */

    namespace models;

    use lib\BaseDatos;

    class Pedido{

        private int $id;
        private int $usuarioId;
        private string $comunidad;
        private string $provincia;
        private string $municipio;
        private string $poblacion;
        private string $nucleo;
        private string $codigoPostal;
        private string $direccion;
        private float $coste;
        private string $estado;
        private string $fecha;
        private string $hora;

        // Getters y Setters

        public function getId(): int{
            return $this->id;
        }

        public function getUsuarioId(): int{
            return $this->usuarioId;
        }

        public function getComunidad(): string{
            return $this->comunidad;
        }

        public function getProvincia(): string{
            return $this->provincia;
        }

        public function getMunicipio(): string{
            return $this->municipio;
        }

        public function getPoblacion(): string{
            return $this->poblacion;
        }

        public function getNucleo(): string{
            return $this->nucleo;
        }

        public function getCodigoPostal(): string{
            return $this->codigoPostal;
        }

        public function getDireccion(): string{
            return $this->direccion;
        }

        public function getCoste(): float{
            return $this->coste;
        }

        public function getEstado(): string{
            return $this->estado;
        }

        public function getFecha(): string{
            return $this->fecha;
        }

        public function getHora(): string{
            return $this->hora;
        }

        public function setId(int $id): void{
            $this->id = $id;
        }

        public function setUsuarioId(int $usuarioId): void{
            $this->usuarioId = $usuarioId;
        }

        public function setComunidad(string $comunidad): void{
            $this->comunidad = $comunidad;
        }

        public function setProvincia(string $provincia): void{
            $this->provincia = $provincia;
        }

        public function setMunicipio(string $municipio): void{
            $this->municipio = $municipio;
        }

        public function setPoblacion(string $poblacion): void{
            $this->poblacion = $poblacion;
        }

        public function setNucleo(string $nucleo): void{
            $this->nucleo = $nucleo;
        }

        public function setCodigoPostal(string $codigoPostal): void{
            $this->codigoPostal = $codigoPostal;
        }

        public function setDireccion(string $direccion): void{
            $this->direccion = $direccion;
        }

        public function setCoste(float $coste): void{
            $this->coste = $coste;
        }

        public function setEstado(string $estado): void{
            $this->estado = $estado;
        }

        public function setFecha(string $fecha): void{
            $this->fecha = $fecha;
        }

        public function setHora(string $hora): void{
            $this->hora = $hora;
        }

        // Métodos dinámicos

        public function save(): bool{

            $baseDatos = new BaseDatos();

            $baseDatos->ejecutar("INSERT INTO pedidos(usuario_id, comunidad, provincia, municipio, poblacion, nucleo, codigo_postal, direccion, coste, estado, fecha, hora) VALUES(:usuario_id, :comunidad, :provincia, :municipio, :poblacion, :nucleo, :codigo_postal, :direccion, :coste, :estado, :fecha, :hora)", [
                ':usuario_id' => $this->usuarioId,
                ':comunidad' => $this->comunidad,
                ':provincia' => $this->provincia,
                ':municipio' => $this->municipio,
                ':poblacion' => $this->poblacion,
                ':nucleo' => $this->nucleo,
                ':codigo_postal' => $this->codigoPostal,
                ':direccion' => $this->direccion,
                ':coste' => $this->coste,
                ':estado' => $this->estado,
                ':fecha' => $this->fecha,
                ':hora' => $this->hora
            ]);

            if ($baseDatos->getNumeroRegistros() == 1) $this->setId($baseDatos->getLastId());
        
            $output = $baseDatos->getNumeroRegistros() == 1;
            $baseDatos->cerrarConexion();
            return $output;

        }

        public function update(): bool{

            $baseDatos = new BaseDatos();

            $baseDatos->ejecutar("UPDATE pedidos SET usuario_id = :usuario_id, comunidad = :comunidad, provincia = :provincia, municipio = :municipio, poblacion = :poblacion, nucleo = :nucleo, codigo_postal = :codigo_postal, direccion = :direccion, coste = :coste, estado = :estado, fecha = :fecha, hora = :hora WHERE id = :id", [
                ':id' => $this->id,
                ':usuario_id' => $this->usuarioId,
                ':comunidad' => $this->comunidad,
                ':provincia' => $this->provincia,
                ':municipio' => $this->municipio,
                ':poblacion' => $this->poblacion,
                ':nucleo' => $this->nucleo,
                ':codigo_postal' => $this->codigoPostal,
                ':direccion' => $this->direccion,
                ':coste' => $this->coste,
                ':estado' => $this->estado,
                ':fecha' => $this->fecha,
                ':hora' => $this->hora
            ]);

            $output = $baseDatos->getNumeroRegistros() == 1;
            $baseDatos->cerrarConexion();
            return $output;

        }

        public function delete(): bool {

            $baseDatos = new BaseDatos();
            $baseDatos->ejecutar("SELECT MAX(id) AS id FROM pedidos");
            $maxId = $baseDatos->getNextRegistro();
            $maxId = $maxId ? $maxId['id'] : null;

            $baseDatos->ejecutar("DELETE FROM pedidos WHERE id = :id", [
                ':id' => $this->id
            ]);

            $output = $baseDatos->getNumeroRegistros() == 1;

            if ($output && $this->id == $maxId) {
                
                $baseDatos->ejecutar("SELECT MAX(id) AS id FROM pedidos");
                $nuevoMaxId = $baseDatos->getNextRegistro();
                $nuevoMaxId = $nuevoMaxId ? $nuevoMaxId['id'] : 0;
                $nuevoAutoIncrement = $nuevoMaxId + 1; // Si la tabla está vacía, empieza en 1
                $baseDatos->ejecutar("ALTER TABLE pedidos AUTO_INCREMENT = $nuevoAutoIncrement");

            }

            $baseDatos->cerrarConexion();
            return $output;
            
        }

        // Métodos estáticos

        public static function getById(int $id): ?Pedido{

            $baseDatos = new BaseDatos();

            $baseDatos->ejecutar("SELECT * FROM pedidos WHERE id = :id", [
                ':id' => $id
            ]);

            if($baseDatos->getNumeroRegistros() == 1){

                $registro = $baseDatos->getNextRegistro();

                $pedido = new Pedido();

                $pedido->setId($registro['id']);
                $pedido->setUsuarioId($registro['usuario_id']);
                $pedido->setComunidad($registro['comunidad']);
                $pedido->setProvincia($registro['provincia']);
                $pedido->setMunicipio($registro['municipio']);
                $pedido->setPoblacion($registro['poblacion']);
                $pedido->setNucleo($registro['nucleo']);
                $pedido->setCodigoPostal($registro['codigo_postal']);
                $pedido->setDireccion($registro['direccion']);
                $pedido->setCoste($registro['coste']);
                $pedido->setEstado($registro['estado']);
                $pedido->setFecha($registro['fecha']);
                $pedido->setHora($registro['hora']);

                return $pedido;

            }
            
            $baseDatos->cerrarConexion();
            return null;

        }

        public static function getByUsuario(int $usuarioId): array{
            
            $baseDatos = new BaseDatos();

            $baseDatos->ejecutar("SELECT * FROM pedidos WHERE usuario_id = :usuario_id", [
                ':usuario_id' => $usuarioId
            ]);

            $registros = $baseDatos->getRegistros();
            $pedidos = [];

            foreach ($registros as $registro) {

                $pedido = new Pedido();

                $pedido->setId($registro['id']);
                $pedido->setUsuarioId($registro['usuario_id']);
                $pedido->setComunidad($registro['comunidad']);
                $pedido->setProvincia($registro['provincia']);
                $pedido->setMunicipio($registro['municipio']);
                $pedido->setPoblacion($registro['poblacion']);
                $pedido->setNucleo($registro['nucleo']);
                $pedido->setCodigoPostal($registro['codigo_postal']);
                $pedido->setDireccion($registro['direccion']);
                $pedido->setCoste($registro['coste']);
                $pedido->setEstado($registro['estado']);
                $pedido->setFecha($registro['fecha']);
                $pedido->setHora($registro['hora']);

                array_push($pedidos, $pedido);
                
            }

            return $pedidos;

        }

        public static function getAll(): array{
            
            $baseDatos = new BaseDatos();
            $baseDatos->ejecutar("SELECT * FROM pedidos");
            $registros = $baseDatos->getRegistros();
            $pedidos = [];

            foreach ($registros as $registro) {

                $pedido = new Pedido();

                $pedido->setId($registro['id']);
                $pedido->setUsuarioId($registro['usuario_id']);
                $pedido->setComunidad($registro['comunidad']);
                $pedido->setProvincia($registro['provincia']);
                $pedido->setMunicipio($registro['municipio']);
                $pedido->setPoblacion($registro['poblacion']);
                $pedido->setNucleo($registro['nucleo']);
                $pedido->setCodigoPostal($registro['codigo_postal']);
                $pedido->setDireccion($registro['direccion']);
                $pedido->setCoste($registro['coste']);
                $pedido->setEstado($registro['estado']);
                $pedido->setFecha($registro['fecha']);
                $pedido->setHora($registro['hora']);

                array_push($pedidos, $pedido);
                
            }

            $baseDatos->cerrarConexion();
            return $pedidos;

        }

    }