<html>
<head>
<script type="text/javascript">
function registroUser() {
	window.alert("Usuario regitrado con éxito");
	document.location.href="index.php";
}
function modificarUser() {
	window.alert("Usuario actualizado con éxito");
	document.location.href="index.php?orden=VerUsuarios";
}
</script>
</head>
<?php 
include_once 'config.php';
include_once 'AccesoDatos.php';

/*
 * DATOS DE USUARIO
 * • Identificador ( 5 a 10 caracteres, no debe existir previamente, solo letras y números)
 * • Contraseña ( 8 a 15 caracteres, debe ser segura)
 * • Nombre ( Nombre y apellidos del usuario
 * • Correo electrónico ( Valor válido de dirección correo, no debe existir previamente)
 * • Tipo de Plan (0-Básico |1-Profesional |2- Premium| 3- Máster)
 * • Estado: (A-Activo | B-Bloqueado |I-Inactivo )
 */
// Inicializo el modelo
// Cargo los datos del fichero a la session
function modeloUserInit()
{
    if (! isset($_SESSION['tusuarios'])) {
        $tabla = [];
        $db = AccesoDatos::getModelo();
        $tusuarios = $db->getUsuarios();
        foreach ($tusuarios as $clave => $user) {

            $tabla[$user->id] = [
                'nombre' => $user->nombre,
                'password' => $user->password,
                'correo' => $user->correo,
                'plan' => $user->plan,
                'estado' => $user->estado
            ];
        }

        $_SESSION['tusuarios'] = $tabla;
    }
}

// Comprueba usuario y contraseña (boolean)
function modeloOkUser($user,$clave){
    $resu = false;
    if (isset ($_SESSION['tusuarios'][$user])) {
        $userdat = $_SESSION['tusuarios'][$user];
        $userclave = $userdat['password'];
        $resu =($clave == $userclave);
    }
    return $resu;
}

// Devuelve el plan de usuario (String)
function modeloObtenerTipo($user){
    
    $nplan = $_SESSION['tusuarios'][$user]['plan'];
    return PLANES[$nplan]; // Máster
}

// Borrar un usuario (boolean)
function modeloUserDel($userid){

    if (isset($_SESSION['tusuarios'][$userid])){ 
        $db = AccesoDatos::getModelo();
        $tuser = $db->borrarUsuario($userid);
        unset($_SESSION['tusuarios'][$userid]);
        return true;
    }
    return false;

}

// Añadir un nuevo usuario (boolean)
function modeloUserAdd($userid,$userdat){
    if (! isset($_SESSION['tusuarios'][$userid]) && $userid !== "" ){
       
       $_SESSION['tusuarios'][$userid]= $userdat;
    
        $user = new Usuario();
        $user->id = $userdat['id'];
        $user->nombre  = $userdat['nombre'];
        $user->password   =  $userdat['password'];
        $user->correo   =  $userdat['correo'];
        $user->plan   =  $userdat['plan'];
        $user->estado   =  $userdat['estado'];
        $db = AccesoDatos::getModelo();
        $db->addUsuario($user);
        
        echo "<script>registroUser();</script>"; 
   }
 return false;
}

// Actualizar un nuevo usuario (boolean)
function modeloUserUpdate ($userid,$userdat){
    if ( isset($_SESSION['tusuarios'][$userid])){
        $_SESSION['tusuarios'][$userid]= $userdat;
        
        $user = new Usuario();
        $user->id = $userid;
        $user->nombre  = $userdat['nombre'];
        $user->password   =  $userdat['password'];
        $user->correo   =  $userdat['correo'];
        $user->plan   =  $userdat['plan'];
        $user->estado   =  $userdat['estado'];
        $db = AccesoDatos::getModelo();
        $db->modUsuario($user);
        
        echo "<script>modificarUser();</script>"; 

    }
    return false;
}

// Tabla de todos los usuarios para visualizar
function modeloUserGetAll (){
    // Genero lo datos para la vista que no muestra la contraseña ni los códigos de estado o plan
    // sino su traducción a texto
    $db = AccesoDatos::getModelo();
    $tusuarios = $db->getUsuarios();
    foreach ($tusuarios as $clave=>$user){
        
        $tabla[$user->id]= ['nombre'=>$user->nombre,
            'correo'=>$user->correo,
            'plan'=>PLANES[$user->plan],
            'estado'=>ESTADOS[$user->estado],
        ];
    }
    return $tabla;
}
// Datos de un usuario para visualizar
function modeloUserGet ($userid){
    if ( isset($_SESSION['tusuarios'][$userid])){
        $db = AccesoDatos::getModelo();
        return $db->getUsuario ($userid);
    }
    return false;
}

function limpiarEntrada(string $entrada):string{
    $salida = trim($entrada); // Elimina espacios antes y después de los datos
    $salida = stripslashes($salida); // Elimina backslashes \
    $salida = strip_tags($salida); // Elimina etiquetas html
    $salida = htmlspecialchars($salida); // Traduce caracteres especiales en entidades HTML
    return $salida;
}
// Función para limpiar todos elementos de un array
function limpiarArrayEntrada(array &$entrada){
    
    foreach ($entrada as $key => $value ) {
        $entrada[$key] = limpiarEntrada($value);
    }
}

function compruebaFormato($userid, $userdat, $clave2) {
    
    if (isset ($_SESSION['tusuarios'][$userid]) == $userid) {return $msg = MENSAJES['ID_REPETIDO'];}
    if( !preg_match("/^[a-zA-Z0-9]+$/", $userid)) {return $msg = MENSAJES['ID_FORMATO'];} // solo letras y números
    if ( !filter_var($userdat['correo'], FILTER_VALIDATE_EMAIL)) {return $msg = MENSAJES['CORREO_FORMATO']; }
    foreach ($_SESSION['tusuarios'] as $clave => $valor){
        if ($valor['correo'] == $userdat['correo'] ) {return $msg = MENSAJES['CORREO_REPETIDO']; }
    }

    if ($userdat['password'] != $clave2) {return $msg = MENSAJES['PASS_NOIGUALES']; } 
    else {
        if(strlen($userdat['password']) < 8){return $msg = MENSAJES['PASS_CORTA'];}        
        if(strlen($userdat['password']) > 15){return $msg = MENSAJES['PASS_LARGA'];}        
        if (!preg_match('`[a-z]`',$userdat['password'])){return $msg = MENSAJES['PASS_NOSEGURA'];}        
        if (!preg_match('`[A-Z]`',$userdat['password'])){return $msg = MENSAJES['PASS_NOSEGURA'];}        
        if (!preg_match('`[0-9]`',$userdat['password'])){return $msg = MENSAJES['PASS_NOSEGURA'];}
    }
    return modeloUserAdd($userid,$userdat);
}

function modificar($userid, $datos) {
   
    if ( !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {return $msg = MENSAJES['CORREO_FORMATO']; }
    foreach ($_SESSION['tusuarios'] as $clave => $valor){
        if ($valor['correo'] == $datos['correo']&& $valor['correo'] != $_SESSION['tusuarios'][$userid]['correo'] ) {return $msg = MENSAJES['CORREO_REPETIDO']; }
    }
    if ($datos['password'] != $datos['password2']) {return $msg = MENSAJES['PASS_NOIGUALES']; }
    else {
        if(strlen($datos['password']) < 8){return $msg = MENSAJES['PASS_CORTA'];}
        if(strlen($datos['password']) > 15){return $msg = MENSAJES['PASS_LARGA'];}        
        if (!preg_match('`[a-z]`',$datos['password'])){return $msg = MENSAJES['PASS_NOSEGURA'];}        
        if (!preg_match('`[A-Z]`',$datos['password'])){return $msg = MENSAJES['PASS_NOSEGURA'];}        
        if (!preg_match('`[0-9]`',$datos['password'])){return $msg = MENSAJES['PASS_NOSEGURA'];}
    }
    return modeloUserUpdate($userid,$datos);
}