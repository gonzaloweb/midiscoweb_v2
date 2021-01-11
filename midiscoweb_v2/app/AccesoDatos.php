<?php
include_once "Usuario.php";
include_once "config.php";

/*
 * Acceso a datos con BD Usuarios y Patrón Singleton 
 * Un único objeto para la clase
 */
class AccesoDatos {
    
    private static $modelo = null;
    private $dbh = null;
    private $stmt_usuarios = null;
    private $stmt_usuario  = null;
    private $stmt_boruser  = null;
    private $stmt_moduser  = null;
    private $stmt_creauser = null;
    
    public static function getModelo(){
        if (self::$modelo == null){
            self::$modelo = new AccesoDatos();
        }
        return self::$modelo;
    }
    
    

   // Constructor privado  Patron singleton
   
    private function __construct(){
        
        try {
            $dsn = "mysql:host=".SERVER_DB.";dbname=Usuarios;charset=utf8";
            $this->dbh = new PDO($dsn, "root", "root");
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e){
            echo "Error de conexión ".$e->getMessage();
            exit();
        }
        // Construyo las consultas
        $this->stmt_usuarios  = $this->dbh->prepare("select * from Usuarios");
        $this->stmt_usuario   = $this->dbh->prepare("select * from Usuarios where id=:id");
        $this->stmt_boruser   = $this->dbh->prepare("delete from Usuarios where id =:id");
        $this->stmt_moduser   = $this->dbh->prepare("update Usuarios set nombre=:nombre, password=:password, correo=:correo, plan=:plan, estado=:estado where id=:id");
        $this->stmt_creauser  = $this->dbh->prepare("insert into Usuarios (id,nombre,password,correo,plan,estado) Values(?,?,?,?,?,?)");
    }

    // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
    public static function closeModelo(){
        if (self::$modelo != null){
            $this->stmt_usuarios = null;
            $this->stmt_usuario  = null;
            $this->stmt_boruser  = null;
            $this->stmt_moduser  = null;
            $this->stmt_creauser = null;
            $this->dbh = null;
            self::$modelo = null; // Borro el objeto.
        }
    }


    // Devuelvo la lista de Usuarios
    public function getUsuarios ():array {
        $tuser = [];
        $this->stmt_usuarios->setFetchMode(PDO::FETCH_CLASS, 'Usuario');
        
        if ( $this->stmt_usuarios->execute() ){
            while ( $user = $this->stmt_usuarios->fetch()){
                $tuser[]= $user;
            }
        }
        return $tuser;
    }

    
    // Devuelvo un usuario o false
    public function getUsuario (String $id) {
        $user = false;
        
        $this->stmt_usuario->setFetchMode(PDO::FETCH_CLASS, 'Usuario');
        $this->stmt_usuario->bindParam(':id', $id);
        if ( $this->stmt_usuario->execute() ){
             if ( $obj = $this->stmt_usuario->fetch()){
                $user= $obj;
            }
        }
        return $user;
    }
    
    // UPDATE
    public function modUsuario($user):bool{
      
        $this->stmt_moduser->bindValue(':id',$user->id);
        $this->stmt_moduser->bindValue(':nombre',$user->nombre);
        $this->stmt_moduser->bindValue(':password',$user->password);
        $this->stmt_moduser->bindValue(':correo',$user->correo);
        $this->stmt_moduser->bindValue(':plan',$user->plan);
        $this->stmt_moduser->bindValue(':estado',$user->estado);
        $this->stmt_moduser->execute();
        $resu = ($this->stmt_moduser->rowCount () == 1);
        return $resu;
    }

    //INSERT
    public function addUsuario($user):bool{

        $this->stmt_creauser->execute( [$user->id, $user->nombre, $user->password, $user->correo, $user->plan, $user->estado] );
        $resu = ($this->stmt_creauser->rowCount () == 1);
        return $resu;
    }

    //DELETE
    public function borrarUsuario(String $id):bool {
        $this->stmt_boruser->bindParam(':id', $id);
        $this->stmt_boruser->execute();
        $resu = ($this->stmt_boruser->rowCount () == 1);
        return $resu;
    }   
    
     // Evito que se pueda clonar el objeto. (SINGLETON)
    public function __clone()
    { 
        trigger_error('La clonación no permitida', E_USER_ERROR); 
    }
}

