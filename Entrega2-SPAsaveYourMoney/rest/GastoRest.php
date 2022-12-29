<?php

require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/UserMapper.php");

require_once(__DIR__ . "/../model/Gasto.php");
require_once(__DIR__ . "/../model/GastoMapper.php");


require_once(__DIR__ . "/BaseRest.php");

/**
 * Class GastoRest
 *
 * It contains operations for creating, retrieving, updating, deleting and
 * listing gastos, as well as to create comments to gastos.
 *
 * Methods gives responses following Restful standards. Methods of this class
 * are intended to be mapped as callbacks using the URIDispatcher class.
 *
 */
class GastoRest extends BaseRest
{
    private $gastoMapper;
    private $commentMapper;

    public function __construct()
    {
        parent::__construct();

        $this->gastoMapper = new GastoMapper();
    }

    public function getGastos()
    {
        $user="oscar";
        $gastos = $this->gastoMapper->findGastosByUsername($user);
        //print_r($gastos);

        // json_encode Gasto objects.
        // since Gasto objects have private fields, the PHP json_encode will not
        // encode them, so we will create an intermediate array using getters and
        // encode it finally
        $gastos_array = array();
        foreach ($gastos as $gasto) {
            
            array_push($gastos_array, array(
                "id" => $gasto["id"],
                "usuario" => $gasto["usuario"],
                "tipo" => $gasto["tipo"] ,
                "cantidad" => $gasto["cantidad"] ,
                "fecha" =>$gasto["fecha"] ,
                "description"=> $gasto["descripcion"],
                "uuidFichero"=>$gasto["fichero"]  
            ));
        }

        header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
        header('Content-Type: application/json');
        echo (json_encode($gastos_array));
    }

    public function getGasto($data)
    {
        // find the Gasto object in the database
        $gasto = $this->gastoMapper->findGastoById($data);

        if ($gasto == NULL) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            echo ("Gasto with id " . $data . " not found");
            return;
        }

        $gasto_array = array(
            "id" => $gasto->getId(),
            "usuario" => $gasto->getUsuario(),
            "tipo" => $gasto->getTipo() ,
            "cantidad" => $gasto->getCantidad() ,
            "fecha" =>$gasto->getFecha() ,
            "description"=> $gasto->getDescription(),
            "uuidFichero"=>$gasto->getUuidFichero()  

        );


        header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
        header('Content-Type: application/json');
        echo (json_encode($gasto_array));
    }


    public function createGasto($data)
    {
           
        //$currentUser = parent::authenticateUser();
        $gasto = new Gasto();

        $gasto->setUsuario($data->usuario);
        $gasto->setTipo($data->tipo);
        $gasto->setCantidad($data->cantidad);
        $gasto->setFecha($data->fecha);
        $gasto->setDescription($data->descripcion);
        $gasto->setUuidFichero($data->UuidFichero);

        //print_r($gasto);

        

        try {
            // validate Gasto object
            $gasto->checkIsValidForAdd(); // if it fails, ValidationException

            // save the Gasto object into the database
            $gastoId = $this->gastoMapper->save($gasto);

            // response OK. Also send gasto in content
            header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created');
            header('Location: ' . $_SERVER['REQUEST_URI'] . "/" . $gastoId);
            header('Content-Type: application/json');
            echo (json_encode(array(
                "id" => $gasto->getId(),
                "usuario" => $gasto->getUsuario(),
                "tipo" => $gasto->getTipo() ,
                "cantidad" => $gasto->getCantidad() ,
                "fecha" =>$gasto->getFecha() ,
                "description"=> $gasto->getDescription(),
                "uuidFichero"=>$gasto->getUuidFichero()  
    
            )));
        } catch (ValidationException $e) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            header('Content-Type: application/json');
            echo (json_encode($e->getErrors()));
        }
    }


    public function updateGasto($gastoId, $data )
    {
        //$currentUser = parent::authenticateUser();

        $gasto = $this->gastoMapper->findGastoById($gastoId);
        if ($gasto == NULL) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            echo ("Gasto with id " . $gastoId . " not found");
            return;
        }

        // // Check if the Gasto author is the currentUser (in Session)
        // if ($gasto->getAuthor() != $currentUser) {
        //     header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        //     echo ("you are not the author of this gasto");
        //     return;
        // }

        $gastoUpdate = new Gasto();

        $gastoUpdate->setId($gastoId);
        $gastoUpdate->setTipo($data->tipo);
        $gastoUpdate->setCantidad($data->cantidad);
        $gastoUpdate->setFecha($data->fecha);
        $gastoUpdate->setDescription($data->descripcion);
        $gastoUpdate->setUuidFichero($data->UuidFichero);

        try {
            // validate Gasto object
            //$gasto->checkIsValidForAdd(); // if it fails, ValidationException
            $this->gastoMapper->update($gastoUpdate);
            header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
        } catch (ValidationException $e) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            header('Content-Type: application/json');
            echo (json_encode($e->getErrors()));
        }
    }

    public function deleteGasto($gastoId)
    {
        //$currentUser = parent::authenticateUser();
        $gasto = $this->gastoMapper->findGastoById($gastoId);

        if ($gasto == NULL) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            echo ("Gasto with id " . $gastoId . " not found");
            return;
        }

        // Check if the Gasto author is the currentUser (in Session)
        // if ($gasto->getAuthor() != $currentUser) {
        //     header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        //     echo ("you are not the author of this gasto");
        //     return;
        // }

        $this->gastoMapper->delete($gasto); 

        header($_SERVER['SERVER_PROTOCOL'] . ' 204 No Content');
    }

}

// URI-MAPPING for this Rest endpoint
$gastoRest = new GastoRest();
URIDispatcher::getInstance()
    ->map("GET",    "/gasto", array($gastoRest, "getGastos"))
    ->map("GET",    "/gasto/$1", array($gastoRest, "getGasto"))
    ->map("POST", "/gasto", array($gastoRest, "createGasto"))
    ->map("PUT",    "/gasto/$1", array($gastoRest, "updateGasto"))
    ->map("DELETE", "/gasto/$1", array($gastoRest, "deleteGasto"));
