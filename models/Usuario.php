<?php

    /**
     * Modelo de los Usuarios
     * 
     * Contiene los métodos...
     * save(): para guardar un nuevo usuario en la base de datos
     * login(): verifica las credenciales de un usuario y inicia el objeto Usuario si sus credenciales son correctas
     * update(): para actualizar los datos de un usuario existente en la base de datos
     * delete(): para eliminar un usuario de la base de datos
     * getById(): para obtener un objeto Usuario por su ID
     * getByEmail(): para obtener un objeto Usuario por su email
     * getAll(): para obtener un array de objetos Usuario con todos los usuarios de la base de datos
     * getPosicion(): para obtener la posición de un usuario en la tabla de usuarios
     */

    namespace models;

    use lib\BaseDatos;

    class Usuario {

        private int $id;
        private string $nombre;
        private string $apellidos;
        private string $email;
        private ?string $password;
        private ?string $rol;
        private ?string $imagen;
        private ?string $color;

        // Getters y Setters

        public function getId(): int{
            return $this->id;
        }

        public function getNombre(): string{
            return $this->nombre;
        }

        public function getApellidos(): string{
            return $this->apellidos;
        }

        public function getEmail(): string{
            return $this->email;
        }

        public function getPassword(): ?string{
            return $this->password;
        }

        public function getRol(): ?string{
            return $this->rol;
        }

        public function getImagen(): ?string{
            return $this->imagen;
        }

        public function getColor(): ?string{
            return $this->color;
        }

        public function setId(int $id): void{
            $this->id = $id;
        }
        
        public function setNombre(string $nombre): void{
            $this->nombre = $nombre;
        }

        public function setApellidos(string $apellidos): void{
            $this->apellidos = $apellidos;
        }

        public function setEmail(string $email): void{
            $this->email = $email;
        }

        public function setPassword(?string $password): void{
            $this->password = $password;
        }

        public function setRol(?string $rol): void{
            $this->rol = $rol;
        }

        public function setImagen(?string $imagen): void{
            $this->imagen = $imagen;
        }

        public function setColor(?string $color): void{
            $this->color = $color;
        }

        // Método auxiliar

        public function getPosicion(): int{

            $baseDatos = new BaseDatos();

            $baseDatos->ejecutar("SELECT COUNT(*) + 1 AS position FROM usuarios WHERE id < :id", [
                ':id' => $this->id
            ]);

            $registro = $baseDatos->getNextRegistro();
            $posicion = $registro ? $registro['position'] : 0;
            $baseDatos->cerrarConexion();
            return $posicion;

        }

        // Métodos dinámicos

        public function save(): bool{

            $baseDatos = new BaseDatos();
            
            $baseDatos->ejecutar("INSERT INTO usuarios VALUES(null, :nombre, :apellidos, :email, :password, :rol, null, :color)", [
                ':nombre' => $this->nombre,
                ':apellidos' => $this->apellidos,
                ':email' => $this->email,
                ':password' => password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 12]),
                ':rol' => $this->rol,
                ':color' => $this->color
            ]);

            $output = $baseDatos->getNumeroRegistros() == 1;
            $baseDatos->cerrarConexion();
            return $output;

        }

        public function login(): ?Usuario{

            $baseDatos = new BaseDatos();

            // Buscar el usuario en la base de datos
            
            $baseDatos->ejecutar("SELECT * FROM usuarios WHERE email = :email", [
                ':email' => $this->email,
            ]);

            // Si se encontró un usuario con ese email

            if($baseDatos->getNumeroRegistros() == 1){
                
                $usuario = $baseDatos->getNextRegistro();

                // Verificar la contraseña
                
                if(password_verify($this->password, $usuario['password'])){

                    $this->setId($usuario['id']);
                    $this->setNombre($usuario['nombre']);
                    $this->setApellidos($usuario['apellidos']);
                    $this->setEmail($usuario['email']);
                    $this->setRol($usuario['rol']);
                    $this->setColor($usuario['color']);
                    $this->setImagen($usuario['imagen']);

                    $baseDatos->cerrarConexion();

                    return $this;

                }

            }

            $baseDatos->cerrarConexion();
            return null;

        }

        public function update(): bool {

            $baseDatos = new BaseDatos();

            if(!$this->password || strlen($this->password) == 0) $this->password = null;
            
            if(!isset($this->rol)) $this->rol = 'user';
            
            if(!isset($this->imagen)) $this->imagen = Usuario::getById($this->id)->getImagen();

            if($this->password !== null) {
                
                // Si se ha introducido una nueva contraseña, se actualiza el campo 'password'

                $query = "UPDATE usuarios 
                          SET nombre = :nombre, apellidos = :apellidos, email = :email, password = :password, rol = :rol, imagen = :imagen, color = :color
                          WHERE id = :id";

                $params = [
                    ':nombre' => $this->nombre,
                    ':apellidos' => $this->apellidos,
                    ':email' => $this->email,
                    ':password' => password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 12]),
                    ':rol' => $this->rol,
                    ':imagen' => $this->imagen,
                    ':color' => $this->color,
                    ':id' => $this->id,
                ];

            }else {

                // Si no se ha introducido una nueva contraseña, se actualizan los demás campos sin tocar 'password'

                $query = "UPDATE usuarios 
                          SET nombre = :nombre, apellidos = :apellidos, email = :email, rol = :rol, imagen = :imagen, color = :color
                          WHERE id = :id";

                $params = [
                    ':nombre' => $this->nombre,
                    ':apellidos' => $this->apellidos,
                    ':email' => $this->email,
                    ':rol' => $this->rol,
                    ':imagen' => $this->imagen,
                    ':color' => $this->color,
                    ':id' => $this->id,
                ];

            }
        
            $baseDatos->ejecutar($query, $params);
            $output = $baseDatos->getNumeroRegistros() == 1;
            $baseDatos->cerrarConexion();
            return $output;

        }

        public function delete(): bool {

            $baseDatos = new BaseDatos();
            $baseDatos->ejecutar("SELECT MAX(id) AS id FROM usuarios");
            $maxId = $baseDatos->getNextRegistro();
            $maxId = $maxId ? $maxId['id'] : null;

            $baseDatos->ejecutar("DELETE FROM usuarios WHERE id = :id", [
                ':id' => $this->id
            ]);
            
            $output = $baseDatos->getNumeroRegistros() == 1;

            if ($output && $this->id == $maxId) {
                
                $baseDatos->ejecutar("SELECT MAX(id) AS id FROM usuarios");
                $nuevoMaxId = $baseDatos->getNextRegistro();
                $nuevoMaxId = $nuevoMaxId ? $nuevoMaxId['id'] : 0;
                $nuevoAutoIncrement = $nuevoMaxId + 1;
                $baseDatos->ejecutar("ALTER TABLE usuarios AUTO_INCREMENT = $nuevoAutoIncrement");

            }

            $baseDatos->cerrarConexion();
            return $output;

        }

        // Métodos estáticos

        public static function getById(int $id): ?Usuario {

            $baseDatos = new BaseDatos();

            $baseDatos->ejecutar("SELECT * FROM usuarios WHERE id = :id", [
                ':id' => $id
            ]);

            if($baseDatos->getNumeroRegistros() == 1){

                $registro = $baseDatos->getNextRegistro();

                $usuario = new Usuario();
                
                $usuario->setId($registro['id']);
                $usuario->setNombre($registro['nombre']);
                $usuario->setApellidos($registro['apellidos']);
                $usuario->setEmail($registro['email']);
                $usuario->setPassword($registro['password']);
                $usuario->setRol($registro['rol']);
                $usuario->setImagen($registro['imagen']);
                $usuario->setColor($registro['color']);

                $baseDatos->cerrarConexion();

                return $usuario;

            }

            $baseDatos->cerrarConexion();
            return null;

        }

        public static function getByEmail(string $email): ?Usuario {

            $baseDatos = new BaseDatos();

            $baseDatos->ejecutar("SELECT * FROM usuarios WHERE email = :email", [
                ':email' => $email
            ]);

            if($baseDatos->getNumeroRegistros() == 1){

                $registro = $baseDatos->getNextRegistro();
                $usuario = new Usuario();

                $usuario->setId($registro['id']);
                $usuario->setNombre($registro['nombre']);
                $usuario->setApellidos($registro['apellidos']);
                $usuario->setEmail($registro['email']);
                $usuario->setRol($registro['rol']);
                $usuario->setImagen($registro['imagen']);
                $usuario->setColor($registro['color']);

                $baseDatos->cerrarConexion();
                return $usuario;

            }

            $baseDatos->cerrarConexion();
            return null;

        }

        public static function getAll(): array {

            $baseDatos = new BaseDatos();
            $baseDatos->ejecutar("SELECT * FROM usuarios");
            $registros = $baseDatos->getRegistros();
            $usuarios = [];
        
            foreach ($registros as $registro) {

                $usuario = new Usuario();

                $usuario->setId($registro['id']);
                $usuario->setNombre($registro['nombre']);
                $usuario->setApellidos($registro['apellidos']);
                $usuario->setEmail($registro['email']);
                $usuario->setRol($registro['rol']);
                $usuario->setImagen($registro['imagen']);
                $usuario->setColor($registro['color']);
        
                $usuarios[] = $usuario;
                
            }

            $baseDatos->cerrarConexion();
            return $usuarios;
            
        }

    }