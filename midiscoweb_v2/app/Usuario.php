<?php
//[ "Nombre","login","Password","Comentario"];

class Usuario
{
    private $id;
    private $nombre;
    private $password;
    private $correo;
    private $plan;
    private $estado;
    
    // Getter con método mágico
    public function __get($atributo){
        if(property_exists($this, $atributo)) {
            return $this->$atributo;
        }
    }
    // Setter con método mágico
    public function __set($atributo,$valor){
        if(property_exists($this, $atributo)) {
            $this->$atributo = $valor;
        }
    }
    
}

