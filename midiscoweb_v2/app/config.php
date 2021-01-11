<?php

define ('GESTIONUSUARIOS','1');
define ('GESTIONFICHEROS','2');

// Fichero donde se guardan los datos o BASE DE DATOS
define('SERVER_DB','192.168.1.174');
define('FILEUSER','app/dat/usuarios.json');
// Ruta donde se guardan los archivos de los usuarios
// Tiene que tener permiso 777 o permitir a Apache rwx
define('RUTA_FICHEROS','/home/Escritorio/phpb/actividades/midiscoweb/dirpruebas');

// (0-Básico |1-Profesional |2- Premium| 3- Máster)
const  PLANES = ['Básico','Profesional','Premium','Máster'];
//  Estado: (A-Activo | B-Bloqueado |I-Inactivo )
const  ESTADOS = ['A' => 'Activo','B' =>'Bloqueado', 'I' => 'Inactivo']; 

// Definir otras constantes 

const MENSAJES = [
    'ID_REPETIDO' => "ERROR:<br> El ID para el registro ya existe<br>",
    'ID_FORMATO' => "ERROR:<br> El ID para el registro solo puede tener letras y números<br>",
    'PASS_NOIGUALES' => "ERROR:<br> Las contraseñas introducidas no coinciden<br>",
    'PASS_NOSEGURA' => "ERROR:<br> La contraseña no es segura<br>",
    'PASS_CORTA'    => "ERROR:<br> La contraseña debe tener al menos 8 caracteres<br>",
    'PASS_LARGA'    => "ERROR:<br> La contraseña debe ser menor de 15 caracteres<br>",
    'CORREO_FORMATO' => "ERROR:<br>El formato de correo electrónico introducido no es correcto<br>",
    'CORREO_REPETIDO'  => "ERROR:<br> Ya existe la dirección de correo<br>",
];