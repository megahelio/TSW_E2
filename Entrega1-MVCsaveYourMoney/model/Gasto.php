<?php

require_once(__DIR__ . "/../core/ValidationException.php");

class Gasto
{
    private $id;
    private $usuario;
    private $tipo;
    private $cantidad;
    private $fecha;
    private $description;
    private $uuidFichero;

    public function __construct($id=NULL, $usuario = NULL, $tipo = NULL, $cantidad = NULL, $fecha = NULL, $description=NULL, $uuidFichero=NULL)
    {
        $this->id = $id;
        $this->usuario=$usuario;
        $this->tipo = $tipo;
        $this->cantidad = $cantidad;
        $this->fecha = $fecha;
        $this->description = $description;
        $this->uuidFichero = $uuidFichero;
        
    
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setUsuario($usuario)
    {
       $this->usuario=$usuario;
    }
    public function getUsuario()
    {
        return $this->usuario;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }
    public function getTipo()
    {
        return $this->tipo;
    }

    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    }
    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function getFecha()
    {
        return $this->fecha;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
    public function getDescription()
    {
        return $this->description;
    }

    public function setUuidFichero($uuidFichero)
    {
        $this->uuidFichero = $uuidFichero;
    }
    public function getUuidFichero()
    {
        return $this->uuidFichero;
    }

    public function checkIsValidForAdd()
    {
        $errors = array();
        /*Validacion gasto
        if (strlen($this->username) < 5) {
            $errors["username"] = "Username must be at least 5 characters length";
        }*/

        $tipos = (new Tipos)->tipos;


        //comprobación tipos
        foreach ($tipos as $tipo) :
            $errors["tipo"] = "Tipo invalido";
            if($tipo == $this->tipo){
                unset($errors["tipo"]);
                break;
            }
        endforeach;

        //comprobar cantidad

        $cantidad = $this->cantidad;

        if(!is_numeric($cantidad) ){
            $errors["cantidad"] = "La cantidad no es un numero";
        }

        $cantidad = round($cantidad, 2);

        if($cantidad < 0){
            $errors["cantidad"] = "cantidad Negativa";
        }

        $date = strtotime($this->fecha);
        $date = getDate($date); 
        
        if(checkdate(($date["mday"]),$date["mon"],$date["year"])){
            $errors["fecha"] = "fecha invalida";
        }

        $descripcion = $this->description;
        if(str_contains($descripcion,'<') || str_contains($descripcion,'>') || str_contains($descripcion,'\\')){
            $errors["descripcion"] = "descripcion invalida";
        }
        
        if (sizeof($errors) > 0) {
            throw new ValidationException($errors, "user is not valid");
        }
    }
}
