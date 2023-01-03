<?php

require_once(__DIR__ . "/../core/ValidationException.php");
require_once(__DIR__."/Tipos.php");

class Gasto
{
    private $id;
    private $usuario;
    private $tipo;
    private $cantidad;
    private $fecha;
    private $descripcion;
    private $uuidFichero;

    public function __construct($id = NULL, $usuario = NULL, $tipo = NULL, $cantidad = NULL, $fecha = NULL, $descripcion = NULL, $uuidFichero = NULL)
    {
        $this->id = $id;
        $this->usuario = $usuario;
        $this->tipo = $tipo;
        $this->cantidad = $cantidad;
        $this->fecha = $fecha;
        $this->descripcion = $descripcion;
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
        $this->usuario = $usuario;
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

    public function setDescription($descripcion)
    {
        $this->descripcion = $descripcion;
    }
    public function getDescription()
    {
        return $this->descripcion;
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
        //Validacion gasto
        if (strlen($this->usuario) < 5) {
            $errors["usuario"] = "Username must be at least 5 characters length";
        }

        $tipos = (new Tipos)->tipos;


        $errors["tipo"] = "Tipo invalido";
        //comprobación tipos
        foreach ($tipos as $tipo) :
            //degug tipos
            //print($tipo . " " . $this->tipo." ");
            if ($tipo == $this->tipo) {
                unset($errors["tipo"]);
                break; //rompe el bucle for
            }
        endforeach;

        //comprobar cantidad

        $cantidad = $this->cantidad;

        if (!is_numeric($cantidad)) {
            $errors["cantidad"] = "La cantidad no es un numero";
        }

        $cantidad = round($cantidad, 2);

        if ($cantidad < 0) {
            $errors["cantidad"] = "cantidad Negativa";
        }

        $date = strtotime($this->fecha);
        $date = getDate($date);

        if (!checkdate( $date["mon"], $date["mday"], $date["year"])) {
            $errors["fecha"] = "fecha invalida";
        }

        $descripcion = $this->descripcion;
        if (str_contains($descripcion, '<') || str_contains($descripcion, '>') || str_contains($descripcion, '\\')) {
            $errors["descripcion"] = "descripcion invalida";
        }

        if (sizeof($errors) > 0) {
            throw new ValidationException($errors, "gasto is not valid");
        }
    }
}
