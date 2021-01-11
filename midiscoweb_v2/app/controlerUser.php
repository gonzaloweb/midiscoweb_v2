<html>
<head>
<script type="text/javascript">
function registrar(){
   window.alert("rellene todos los campos");
   document.location.href="?orden=Registrarse";
}
function alta(){
   window.alert("Por favor rellene todos los datos");
   document.location.href="?orden=Alta";
}
function borradoUser(){
	window.alert("El Usuario seleccionado ha sido eliminado de la base de datos");
}
function errorBorrado() {
	window.alert("No se ha podido eliminar el usuario");
}
function actualizadoUser() {
	window.alert("El Usuario ha sido actualizado correctamente en la base de datos ");
}
</script>
</head>
<?php
// ------------------------------------------------
// Controlador que realiza la gestión de usuarios
// ------------------------------------------------
include_once 'config.php';
include_once 'modeloUser.php';

/*
 * Inicio Muestra o procesa el formulario (POST)
 */
function ctlUserInicio()
{
    $msg = "";
    $user = "";
    $clave = "";

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        limpiarArrayEntrada($_POST);
        if (isset($_POST['user']) && isset($_POST['clave'])) {
            $user = $_POST['user'];
            $clave = $_POST['clave'];
            if (modeloOkUser($user, $clave)) {
                $_SESSION['user'] = $user;
                $_SESSION['tipouser'] = modeloObtenerTipo($user);
                if ($_SESSION['tipouser'] == "Máster") {
                    $_SESSION['modo'] = GESTIONUSUARIOS;

                    header('Location:index.php?orden=VerUsuarios');
                } else {
                    // Usuario normal;
                    // PRIMERA VERSIÓN SOLO USUARIOS ADMISTRADORES
                    $msg = "Error: Acceso solo permitido a usuarios Administradores.";
                    unset($_SESSION['user']);
                    // $_SESSION['modo'] = GESTIONFICHEROS;
                    // Cambio de modo y redireccion a verficheros
                }
            } else {
                $msg = "Error: usuario y contraseña no válidos.";
            }
        }
    }

    include_once 'plantilla/facceso.php';
}

function ctlUserAlta()
{
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        ctlUserRegistro(); 
    }

    include_once 'plantilla/fnuevo.php';
}

function ctlUserRegistro()
{
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        limpiarArrayEntrada($_POST);
        $userid = $_POST['id'];
        $clave = $_POST['clave'];
        $clave2 = $_POST['clave2'];
        $correo = $_POST['correo'];
        foreach ($_POST as $codigo => $valor) {
            $datos[] = $valor;
        } // almaceno los datos enviados

        if (isset($_POST['estado'])) { // si el registro es modo administrador
            for ($i = 0; $i < count($datos) - 2; $i ++) { // -2 para que no cuente el campo "plan" ni "estado"
                if (empty($datos[$i]) && $datos[$i] !== 0) {

                    echo "<script>alta()</script>"; // obligatorio rellenar todos los campos
                }
            }
        } else { 

            for ($i = 0; $i < count($datos) - 1; $i ++) { // -1 para que no cuente el campo "plan"
                if (empty($datos[$i]) && $datos[$i] !== 0) {

                    echo "<script>registrar()</script>"; // obligatorio rellenar todos los campos
                }
            }
        }
        if (! isset($datos[6])) { // Establece estado inactivo al registrarse cualquier usuario
            $datos[6] = "I"; // si es administrador el que registra el estado lo coge del POST (A,I,B)
        }

        $userdat = [
            'id' => $datos[0],
            'nombre' => $datos[1],
            'password' => $datos[2],
            'correo' => $datos[4],
            'plan' => $datos[5],
            'estado' => $datos[6]
        ];

        if (isset($_POST['estado'])) { // si es administrador incluyo formulario de alta
            $msg = compruebaFormato($userid, $userdat, $clave2);
            include_once 'plantilla/fnuevo.php';
        } else {
            $msg = compruebaFormato($userid, $userdat, $clave2);
        }
    }

    include_once 'plantilla/fregistro.php';
}

function ctlUserModificar()
{
    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        $datos = modeloUserGet($_GET['id']); //

        $userid = $datos->id;
        $clave = $datos->password;
        $clave2 = $datos->password;
        $nombre = $datos->nombre;
        $correo = $datos->correo;
        $plan = $datos->plan;
        $estado = $datos->estado;
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        limpiarArrayEntrada($_POST); // evito inyección de código
        $userid = $_POST['id'];
        $nombre = $_POST['nombre'];
        $clave = $_POST['password'];
        $clave2 = $_POST['password2'];
        $correo = $_POST['correo'];
        $plan = $_POST['plan'];
        $estado = $_POST['estado'];

        foreach ($_POST as $codigo => $valor) {
            if ($codigo == 'id')
                continue;
            $datos[$codigo] = $valor;
        }

        $msg = modificar($userid, $datos);
    }

    include_once 'plantilla/fmodificar.php';
}

function ctlUserBorrar()
{
    $userid = $_GET['id'];
    if (modeloUserDel($userid)) { // ejecutar borrar
        echo "<script>borradoUser()</script>";
    } else {
        echo "<script>errorBorrado()</script>";
    }

    header("refresh:0;url=index.php?orden=VerUsuarios");
}

function ctlUserDetalles()
{
    $datos = modeloUserGet($_GET['id']);
    $userid = $_GET['id'];
    $clave = $datos['password'];
    $nombre = $datos['nombre'];
    $correo = $datos['correo'];
    $plan = PLANES[$datos['plan']];
    $estado = ESTADOS[$datos['estado']];

    include_once 'plantilla/detallesUser.php';
}

// Cierra la sesión y vuelva los datos
function ctlUserCerrar()
{
    session_destroy();
    header('Location:index.php');
}

// Muestro la tabla con los usuario
function ctlUserVerUsuarios()
{
    // Obtengo los datos del modelo
    $usuarios = modeloUserGetAll();
    // Invoco la vista
    include_once 'plantilla/verusuariosp.php';
}
?>
</html>