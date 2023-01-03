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
 * listing gastos.
 *
 * Methods gives responses following Restful standards. Methods of this class
 * are intended to be mapped as callbacks using the URIDispatcher class.
 *
 */
class GastoRest extends BaseRest
{
    private $gastoMapper;

    public function __construct()
    {
        parent::__construct();

        $this->gastoMapper = new GastoMapper();
    }
    /**
     * Emite los gastos del usuario logeado
     * @throws  401 Unauthorized -> no hay usuario logeado
     * @return json gastos del usuario logeado
     */
    public function getGastos()
    {

        $currentUser = parent::authenticateUser()->getUsername();
        $gastos = $this->gastoMapper->findGastosByUsername($currentUser);

        // json_encode Gasto objects.
        // since Gasto objects have private fields, the PHP json_encode will not
        // encode them, so we will create an intermediate array using getters and
        // encode it finally
        $gastos_array = array();
        foreach ($gastos as $gasto) {

            array_push($gastos_array, array(
                "id" => $gasto["id"],
                "usuario" => $gasto["usuario"],
                "tipo" => $gasto["tipo"],
                "cantidad" => $gasto["cantidad"],
                "fecha" => $gasto["fecha"],
                "descripcion" => $gasto["descripcion"],
                "uuidFichero" => $gasto["fichero"]
            ));
        }

        header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
        header('Content-Type: application/json');
        echo (json_encode($gastos_array));
    }

    /**
     * Devuelve el gasto con el id que se pase como parámetro
     * 
     * @throws  400 Bad request -> no existe el id
     * @throws  401 Unauthorized -> no hay usuario logeado
     * @throws  403 Forbidden -> existe el id pero el usuario logeado es distinto al propietario del gasto
     * 
     * @param $data id
     * 
     * @return 200 OK + json información del gasto -> existe el id y el usuario logeado es el propietario del gasto
     */
    public function getGasto($data)
    {
        //CurrentUser String
        $currentUser = parent::authenticateUser()->getUsername();
        // find the Gasto object in the database
        $gasto = $this->gastoMapper->findGastoById($data);

        //verificamos que el gasto existe
        if ($gasto == NULL) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            die("Gasto with id " . $data . " not found");
        }

        //verificamos que el gasto pertenece al usuario que lo solicita
        if ($gasto->getUsuario()->getUsername() != $currentUser) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
            die("Gasto with id " . $data . " not own by you: " . $currentUser);
        }

        $gasto_array = array(
            "id" => $gasto->getId(),
            "usuario" => $gasto->getUsuario()->getUsername(),
            "tipo" => $gasto->getTipo(),
            "cantidad" => $gasto->getCantidad(),
            "fecha" => $gasto->getFecha(),
            "descripcion" => $gasto->getDescription(),
            "uuidFichero" => $gasto->getUuidFichero()

        );


        header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
        header('Content-Type: application/json');
        echo (json_encode($gasto_array));
    }
    /**
     * Guarda el gasto que se pasa como parametro y lo devuelve como confirmación
     * 
     * @throws  400 Bad request -> Error en la validación del gasto
     * @throws  400 Bad request -> No incluye algún campo obligatorio
     * @throws  401 Unauthorized -> No hay usuario logeado
     * @throws  500 Internal Server Error -> GastoMapper no devuelve el gasto creado
     * 
     * @param $data gasto a guardar
     * 
     * @return 200 OK + json información del gasto -> se crea el gasto correctamente
     */

    public function createGasto($data)
    {

        $currentUser = parent::authenticateUser()->getUsername();

        //parseamos Parametro a objeto
        $gasto = new Gasto();

        /*En caso de que no se incluya algún elemento obligatorio lo registraremos
        y enviaremos una bad request con el feedback*/
        $incluyeCamposObligatorios = true;
        $incluyeCamposObligatoriosErrorLog = "[";

        /*El  usuario será el que está logueado, (un usuario no puede crear gastos para otro usuario)*/
        $gasto->setUsuario($currentUser);

        //Campo obligatorio
        if (isset($data->tipo)) {
            $gasto->setTipo($data->tipo);
        } else {
            //Activamos flag de que un elemento obligatorio no existe
            $incluyeCamposObligatorios = false;
            //Incluimos el aviso en el log
            $incluyeCamposObligatoriosErrorLog = $incluyeCamposObligatoriosErrorLog . "tipo ";
        }

        if (isset($data->cantidad)) {
            $gasto->setCantidad($data->cantidad);
        } else {
            $incluyeCamposObligatorios = false;
            $incluyeCamposObligatoriosErrorLog = $incluyeCamposObligatoriosErrorLog . "cantidad ";
        }

        //El formato de la fecha 1999-12-31 funciona
        if (isset($data->fecha)) {
            $gasto->setFecha($data->fecha);
        } else {
            $incluyeCamposObligatorios = false;
            $incluyeCamposObligatoriosErrorLog = $incluyeCamposObligatoriosErrorLog . "fecha ";
        }

        if (isset($data->descripcion)) {
            $gasto->setDescription($data->descripcion);
        } else {
            $incluyeCamposObligatorios = false;
            $incluyeCamposObligatoriosErrorLog = $incluyeCamposObligatoriosErrorLog . "descripcion ";
        }

        //Campo no obligatorio.
        if (isset($data->uuidfichero)) {
            $gasto->setUuidFichero($data->uuidfichero);
        }

        //Respondemos Bad request al no encontrar elemntos obligatorios
        if (!$incluyeCamposObligatorios) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            die($incluyeCamposObligatoriosErrorLog . "] not found");
        }


        try {
            // validate Gasto object
            $gasto->checkIsValidForAdd(); // if it fails, ValidationException


            // save the Gasto object into the database
            $gastoSaved = $this->gastoMapper->save($gasto);

            //si se guarda correctamente
            if (isset($gastoSaved)) {
                // response OK. Also send gasto in content
                header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created');
                header('Location: ' . $_SERVER['REQUEST_URI'] . "/" . $gastoSaved->getId());
                header('Content-Type: application/json');
                echo (json_encode(array(
                    "id" => $gastoSaved->getId(),
                    "usuario" => $gastoSaved->getUsuario()->getUsername(),
                    "tipo" => $gastoSaved->getTipo(),
                    "cantidad" => $gastoSaved->getCantidad(),
                    "fecha" => $gastoSaved->getFecha(),
                    "descripcion" => $gastoSaved->getDescription(),
                    "uuidFichero" => $gastoSaved->getUuidFichero()

                )));
                //si el gasto no se crea correctamente...
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
                die("Gasto might be not created by a server error.");
            }
        } catch (ValidationException $e) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            header('Content-Type: application/json');
            echo (json_encode($e->getErrors()));
        }
    }

    /**
     * Edita el gasto especificado.
     * 
     * @param int gastoId -> id del gasto a modificar
     * @param Gasto data -> Información del nuevo gasto
     * 
     * @throws 400 Bad request -> No existe ningún gasto con esa id
     * @throws 400 Bad request -> La solicitud no contiene campos obligatorios o son invalidos
     * @throws  401 Unauthorized -> No hay usuario logeado
     * @throws 403 Forbidden -> El gasto no pertenece al usuario logueado
     * @throws  500 Internal Server Error -> GastoMapper no devuelve el gasto actualizado
     * 
     * @return 200 OK + json información del gasto -> se actualiza el gasto correctamente
     */
    public function updateGasto($gastoId, $data)
    {


        $currentUser = parent::authenticateUser()->getUsername();

        $gasto = $this->gastoMapper->findGastoById($gastoId);
        if ($gasto == NULL) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            die("Gasto with id " . $gastoId . " not found");
        }

        // Check if the Gasto owner is the currentUser (in Session)
        if ($gasto->getUsuario()->getUsername() != $currentUser) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
            die("You(" . $currentUser . ") must be the owner of the gasto");
        }

        $gastoUpdate = new Gasto();

        /* Para poblar el objeto que se subirá a la base de datos
        a diferencia de la funcionalidad de crear, en el que el usuario introducía los valores finales y estos no podían ser nulos,
        para la funcionalidad de editar el json que se suministra podrá contener datos vacíos. Esto significará una instrucción de "no edición",
        "preservar el valor antiguo".
        En el caso de campos que puedan ser nulos (no obligatorios) como el caso de el fichero usaremos REMOVE para indicar que el nuevo valor debe ser nulo. */

        $gastoUpdate->setId($gastoId);

        $gastoUpdate->setUsuario($currentUser);

        //Campo obligatorio
        if (isset($data->tipo)) {
            $gastoUpdate->setTipo($data->tipo);
        } else {
            $gastoUpdate->setTipo($gasto->getTipo());
        }

        if (isset($data->cantidad)) {
            $gastoUpdate->setCantidad($data->cantidad);
        } else {
            $gastoUpdate->setCantidad($gasto->getCantidad());
        }

        //El formato de la fecha 1999-12-31 funciona
        if (isset($data->fecha)) {
            $gastoUpdate->setFecha($data->fecha);
        } else {
            $gastoUpdate->setFecha($gasto->getFecha());
        }

        if (isset($data->descripcion)) {
            $gastoUpdate->setDescription($data->descripcion);
        } else {
            $gastoUpdate->setDescription($gasto->getDescription());
        }

        //Campo no obligatorio.
        if (isset($data->uuidfichero)) {
            if ($data->uuidfichero == "") {
                $gastoUpdate->setUuidFichero(null);
            } else {
                $gastoUpdate->setUuidFichero($data->uuidfichero);
            }
        } else {
            $gastoUpdate->setUuidFichero($gasto->getUuidFichero());
        }

        try {
            // validate Gasto object
            $gastoUpdate->checkIsValidForAdd(); // if it fails, ValidationException
            $this->gastoMapper->update($gastoUpdate);
            $gastoSaved = $this->gastoMapper->findGastoById($gastoId);//recuperamos el gasto editado
            
            if (isset($gastoSaved)) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
                header('Content-Type: application/json');
                echo (json_encode(array(
                    "id" => $gastoSaved->getId(),
                    "usuario" => $gastoSaved->getUsuario()->getUsername(),
                    "tipo" => $gastoSaved->getTipo(),
                    "cantidad" => $gastoSaved->getCantidad(),
                    "fecha" => $gastoSaved->getFecha(),
                    "descripcion" => $gastoSaved->getDescription(),
                    "uuidFichero" => $gastoSaved->getUuidFichero()

                )));
                //si el gasto no se actualiza correctamente...
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
                die("Gasto might be not updated by a server error.");
            }

        } catch (ValidationException $e) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            header('Content-Type: application/json');
            die(json_encode($e->getErrors()));
        }
    }
    /**
     * Elimina el gasto con el id que se pasa como parámetro
     * 
     * @throws  400 Bad request -> no existe el id
     * @throws  401 Unauthorized -> no hay usuario logeado
     * @throws  403 Forbidden -> existe el id pero el usuario logeado es distinto al propietario del gasto
     * 
     * @param string $data id
     * 
     * @return 204 No Content -> existe el id y el usuario logeado es el propietario del gasto
     */

    public function deleteGasto($gastoId)
    {
        $currentUser = parent::authenticateUser()->getUsername();
        $gasto = $this->gastoMapper->findGastoById($gastoId);

        //Si el id no existe
        if ($gasto == NULL) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request');
            die("Gasto with id " . $gastoId . " not found");
        }

        //Check if the Gasto author is the currentUser (in Session)
        if ($gasto->getUsuario()->getUsername() != $currentUser) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
            die("You (" . $currentUser . ") are not the owner of this gasto.");
        }

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
