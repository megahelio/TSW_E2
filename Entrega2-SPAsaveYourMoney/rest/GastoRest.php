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
        echo "funciona";
        // $user="oscar";
        // $gastos = $this->gastoMapper->findGastosByUsername($user);

        // // json_encode Gasto objects.
        // // since Gasto objects have private fields, the PHP json_encode will not
        // // encode them, so we will create an intermediate array using getters and
        // // encode it finally
        // $gastos_array = array();
        // foreach ($gastos as $gasto) {
        //     array_push($gastos_array, array(
        //         "id" => $gasto->getId(),
        //         "usuario" => $gasto->getUsuario(),
        //         "tipo" => $gasto->getTipo(),
        //         "cantidad" => $gasto->getCantidad(),
        //         "fecha" => $gasto->getFecha(),
        //         "description"=> $gasto->getDescription(),
        //         "uuidFichero"=> $gasto->getUuidFichero()
        //     ));
        // }

        // header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
        // header('Content-Type: application/json');
        // echo (json_encode($gastos_array));
    }
/*
    public function createGasto($data)
    {
        $currentUser = parent::authenticateUser();
        $gasto = new Gasto();

        if (isset($data->title) && isset($data->content)) {
            $gasto->setTitle($data->title);
            $gasto->setContent($data->content);

            $gasto->setAuthor($currentUser);
        }

        try {
            // validate Gasto object
            $gasto->checkIsValidForCreate(); // if it fails, ValidationException

            // save the Gasto object into the database
            $gastoId = $this->gastoMapper->save($gasto);

            // response OK. Also send gasto in content
            header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created');
            header('Location: ' . $_SERVER['REQUEST_URI'] . "/" . $gastoId);
            header('Content-Type: application/json');
            echo (json_encode(array(
                "id" => $gastoId,
                "title" => $gasto->getTitle(),
                "content" => $gasto->getContent()
            )));
        } catch (ValidationException $e) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            header('Content-Type: application/json');
            echo (json_encode($e->getErrors()));
        }
    }

    public function readGasto($gastoId)
    {
        // find the Gasto object in the database
        $gasto = $this->gastoMapper->findByIdWithComments($gastoId);
        if ($gasto == NULL) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            echo ("Gasto with id " . $gastoId . " not found");
            return;
        }

        $gasto_array = array(
            "id" => $gasto->getId(),
            "title" => $gasto->getTitle(),
            "content" => $gasto->getContent(),
            "author_id" => $gasto->getAuthor()->getusername()

        );

        //add comments
        $gasto_array["comments"] = array();
        foreach ($gasto->getComments() as $comment) {
            array_push($gasto_array["comments"], array(
                "id" => $comment->getId(),
                "content" => $comment->getContent(),
                "author" => $comment->getAuthor()->getusername()
            ));
        }

        header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
        header('Content-Type: application/json');
        echo (json_encode($gasto_array));
    }

    public function updateGasto($gastoId, $data)
    {
        $currentUser = parent::authenticateUser();

        $gasto = $this->gastoMapper->findById($gastoId);
        if ($gasto == NULL) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            echo ("Gasto with id " . $gastoId . " not found");
            return;
        }

        // Check if the Gasto author is the currentUser (in Session)
        if ($gasto->getAuthor() != $currentUser) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
            echo ("you are not the author of this gasto");
            return;
        }
        $gasto->setTitle($data->title);
        $gasto->setContent($data->content);

        try {
            // validate Gasto object
            $gasto->checkIsValidForUpdate(); // if it fails, ValidationException
            $this->gastoMapper->update($gasto);
            header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
        } catch (ValidationException $e) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            header('Content-Type: application/json');
            echo (json_encode($e->getErrors()));
        }
    }

    public function deleteGasto($gastoId)
    {
        $currentUser = parent::authenticateUser();
        $gasto = $this->gastoMapper->findById($gastoId);

        if ($gasto == NULL) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            echo ("Gasto with id " . $gastoId . " not found");
            return;
        }
        // Check if the Gasto author is the currentUser (in Session)
        if ($gasto->getAuthor() != $currentUser) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
            echo ("you are not the author of this gasto");
            return;
        }

        $this->gastoMapper->delete($gasto);

        header($_SERVER['SERVER_PROTOCOL'] . ' 204 No Content');
    }

 */

}

// URI-MAPPING for this Rest endpoint
$gastoRest = new GastoRest();
URIDispatcher::getInstance()
    ->map("GET",    "/gasto", array($gastoRest, "getGastos"))
    ->map("POST", "/gasto", array($gastoRest, "createGasto"))
    ->map("PUT",    "/gasto/$1", array($gastoRest, "updateGasto"))
    ->map("DELETE", "/gasto/$1", array($gastoRest, "deleteGasto"));
