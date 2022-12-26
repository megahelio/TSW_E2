<?php

require_once(__DIR__ . "/../core/ViewManager.php");
require_once(__DIR__ . "/../core/I18n.php");

require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/Gasto.php");
require_once(__DIR__ . "/../model/GastosMapper.php");

require_once(__DIR__ . "/../controller/BaseController.php");

require_once(__DIR__ . "/../model/tipos.php");

/**
 * Class GastosController
 *
 * Controller to login, logout and Gasto registration
 *
 * @author lipido <lipido@gmail.com>
 */
class GastosController extends BaseController
{

    /**
     * Reference to the GastoMapper to interact
     * with the database
     *
     * @var GastoMapper
     */
    private $gastosMapper;

    public function __construct()
    {
        parent::__construct();

        $this->gastosMapper = new GastosMapper();
    }




    private function getPieGraph($gastosData)
    {
        //calcular el total
        $totalEachArray = array();
        $total = 0.0;
        $i = 0;
        $tipos = array_keys($gastosData);

        foreach ($gastosData as $Arraytipo) :
            $totalEach = 0.0;
            foreach ($Arraytipo as $gasto) :
                $total += floatval($gasto["cantidad"]);
                $totalEach += floatval($gasto["cantidad"]);
            endforeach;
            $totalEachArray[$tipos[$i]] = $totalEach;
            $i++;
        endforeach;
        ($total != 0) ?: $total = 1;


        //calcular la fracción dee cada uno
        $mayor = 0;
        $tipoMayor = NULL;
        $dataFraction = array();
        foreach ($tipos as $tipo) :
            $dataFraction[$tipo] = $totalEachArray[$tipo] / $total;
            if ($dataFraction[$tipo] > $mayor) {
                $tipoMayor = $tipo;
                $mayor = $dataFraction[$tipo];
            }
        endforeach;

        //crear el string con la data
        $data = "";
        $j = 0;
        while ($j < count($tipos)) {

            $data = $data . "{\n";
            $data = $data .  "name: '" . i18n($tipos[$j]) . "'," . "\n";
            $data = $data . "y: " .   $dataFraction[$tipos[$j]] . "\n";
            if ($tipos[$j] == $tipoMayor) {
                $data = $data . ",\n";
                $data = $data . "sliced: true,\n selected: true\n";
            }
            $data = $data  . "}";

            if ($j != count($tipos) - 1) {
                $data = $data . ",";
            }
            $j++;
        }


        $this->view->setVariable("pieGraphData", $data);
    }

    private function getLineGraph($gastosData)
    {

        $tipos = array_keys($gastosData);
        // echo ("GastosDataInicial: ");
        // print_r($gastosData);
        $months = array();

        foreach ($tipos as $tipo) :
            // echo ("\ntipos: ");
            // print_r($tipo);
            $iterationCount = 0;
            foreach ($gastosData["$tipo"] as $gastoDelTipo) :
                if (!in_array(substr($gastoDelTipo["fecha"], 0, 7), $months)) {
                    $months[] = substr($gastoDelTipo["fecha"], 0, 7);
                }
                $gastosData[$tipo][substr($gastoDelTipo["fecha"], 0, 7)][] = $gastoDelTipo;
                unset($gastosData[$tipo][$iterationCount]);
                $iterationCount++;
            endforeach;
        endforeach;
        // echo ("\nMonths: ");
        // print_r($months);
        sort($months);
        // echo ("\nMonthsOrder: ");
        // print_r($months);

        $monthsString = "[";
        $iterationCount = 0;
        foreach ($months as $month) :
            if ($iterationCount < sizeof($months) - 1) {
                $monthsString = $monthsString . "'" . $month . "', ";
                $iterationCount++;
            } else {
                $monthsString = $monthsString . "'" . $month . "']";
            }
        endforeach;
        // echo ("\nMonthsString: ");
        // print_r($monthsString);




        $this->view->setVariable("lineGraphMonths", $monthsString);
        // echo ("\nGastosData: ");
        // print_r($gastosData);


        //crear el string con la data
        $gastosDataString = "[";



        $iterationCountTipo = 0;
        foreach ($gastosData as $gastosDataPerTipo) :

            // echo ("\nGastosDataPertipo  : ");
            // print_r($gastosDataPerTipo);
            $iterationCountMonth = 0;
            foreach ($months as $month) :
                $subtotal = 0;
                if ($iterationCountMonth == 0) {
                    $gastosDataString = $gastosDataString . "{name: '" . i18n($gastosDataPerTipo[array_keys($gastosDataPerTipo)[0]][0]["tipo"]) . "', data: [";
                }
                if (isset($gastosDataPerTipo[$month])) {

                    foreach ($gastosDataPerTipo[$month] as $gasto) :

                        $subtotal = $subtotal + $gasto["cantidad"];
                    endforeach;
                }

                if ($iterationCountMonth < sizeof($months) - 1) {
                    $gastosDataString = $gastosDataString . $subtotal . ",";
                } else {
                    if ($iterationCountTipo == sizeof($gastosData) - 1) {
                        $gastosDataString = $gastosDataString . $subtotal . "]} ";
                    } else {
                        $gastosDataString = $gastosDataString . $subtotal . "]}, ";
                    }
                }

                $iterationCountMonth++;
            endforeach;
            $iterationCountTipo++;
        endforeach;


        $gastosDataString = $gastosDataString . "]";




        // echo ("\nGastosString: ");
        // print_r($gastosDataString);
        $this->view->setVariable("lineGraphData", $gastosDataString);
    }


    public function index()
    {
        $firstMonth = date("Y") - 1 . "-" . date("m") . "-01";
        //este churro es el último día del mes
        $lastMonth = date("Y") . "-" . date("m") . "-" . date("d", (mktime(0, 0, 0, date("m") + 1, 1, date("Y")) - 1));
        // echo("Patatas:");
        //print_r($_REQUEST);

        if (isset($_POST["initDate"]) && $_POST["initDate"]!="") {
            
            $firstMonth = $_POST["initDate"];
            (!isset($_POST["endDate"]) || $_POST["endDate"]!="") ?: $lastMonth =  $_POST["endDate"];
            //si meses invertidos, pues se los pongo derechos
            if ($firstMonth > $lastMonth) {
                $lastMonth = $_POST["initDate"];
                $firstMonth = $_POST["endDate"];
            }
        }

        $tipos = (new Tipos)->tipos;
        $gastosData = array();
        $gastos = $this->gastosMapper->findGastosByUsernameByDateRange($this->currentUser->getUsername(), $firstMonth, $lastMonth);;



        foreach ($gastos as $gasto) :
            $gastosData[$gasto["tipo"]][] = $gasto;
        endforeach;

        // echo ("POST: ");
        // print_r($_POST);
        // echo ("\nGASTOS: ");
        // print_r($gastosData);
        // echo ("\nMESES: ");
        // print_r($firstMonth);
        // echo ("  ");
        // print_r($lastMonth);
        $this->getPieGraph($gastosData, $tipos);
        $this->getLineGraph($gastosData, $tipos);

        $this->view->render("gastos", "index");
    }


    public function listarGastos()
    {

        $gastos = $this->gastosMapper->findGastosByUsername($this->currentUser->getUsername());

        $this->view->setVariable("gastos", $gastos);
        $this->view->render("gastos", "listarGastos");
    }

    public function add()
    {
        if (!isset($_POST["tipo"])) {
            $this->view->render("gastos", "add");
        } else {


            if (!isset($this->currentUser)) {
                throw new Exception("Not in session. Adding posts requires login");
            }


            /*Verificar que el que borra el gasto es el dueño del gasto */
            $gasto = new Gasto();

            $gasto->setUsuario($this->currentUser->getUsername());
            $gasto->setTipo($_POST["tipo"]);
            $gasto->setCantidad($_POST["cantidad"]);
            $gasto->setFecha($_POST["fecha"]);
            $gasto->setDescription($_POST["descripcion"]);
            $gasto->setUuidFichero("");

            if ($_FILES["fichero"]["size"] != 0) {
                $RandomFileId = uniqid();
                move_uploaded_file($_FILES["fichero"]["tmp_name"], "uploads/" . $RandomFileId);
                $gasto->setUuidFichero($RandomFileId);
            }

            if ($gasto->getUsuario() != $_SESSION["currentuser"]) {
                throw new Exception("logged user is not the author of the post id ");
                die();
            }

            try {
                // validate Post object
                $gasto->checkIsValidForAdd(); // if it fails, ValidationException

                // save the Post object into the database

                $this->gastosMapper->save($gasto);

                // POST-REDIRECT-GET
                // Everything OK, we will redirect the user to the list of posts
                // We want to see a message after redirection, so we establish
                // a "flash" message (which is simply a Session variable) to be
                // get in the view after redirection.
                $this->view->setFlash(sprintf(i18n("Post \"%s\" successfully added."), $gasto->getDescription()));

                // perform the redirection. More or less:
                // header("Location: index.php?controller=posts&action=index")
                // die();

                $this->view->redirect("gastos", "listarGastos");
            } catch (ValidationException $ex) {
                // Get the errors array inside the exepction...
                $errors = $ex->getErrors();
                // And put it to the view as "errors" variable
                $this->view->setVariable("errors", $errors);
                $this->view->render("gastos", "add");
            }
        }
    }
    public function removeFile()
    {
        $gasto = new Gasto();
        $gasto->setId($_REQUEST["id"]);
        $gasto = $this->gastosMapper->findGastosById($gasto);
        //unlink("uploads/" . $gasto->getUuidFichero());
        $gasto->setUuidFichero(null);
        $this->gastosMapper->update($gasto);

        $this->view->setFlash(sprintf(i18n("Expending file \"%s\" successfully removed."), $gasto->getDescription()));
        $this->view->redirect("gastos", "listarGastos");
    }
    public function edit()
    {
        $gasto = new Gasto();
        $gasto->setId($_REQUEST["id"]);
        $gasto = $this->gastosMapper->findGastosById($gasto);

        if (isset($_POST["tipo"])) {
            if ($gasto->getUsuario() != $this->currentUser) {
                //no puedo por lo que sea
                $this->view->setFlash(sprintf(i18n("Failed to edit \"%s\", do you own the expending?"), $gasto->getDescription()));
                $this->view->redirect("gastos", "listarGastos");
            }

            $gasto->setUsuario($this->currentUser->getUsername());
            $gasto->setTipo($_POST["tipo"]);
            $gasto->setCantidad($_POST["cantidad"]);
            $gasto->setFecha($_POST["fecha"]);
            $gasto->setDescription($_POST["descripcion"]);

            print_r($_FILES["fichero"]);
            print_r($_POST);

            if ($_FILES["fichero"] != null) {
                $RandomFileId = uniqid();
                unlink("uploads/" . $gasto->getUuidFichero());
                move_uploaded_file($_FILES["fichero"]["tmp_name"], "uploads/" . $RandomFileId);
                $gasto->setUuidFichero($RandomFileId);
            }

            $this->gastosMapper->update($gasto);
            $this->view->setFlash(sprintf(i18n("Expending \"%s\" successfully edited."), $gasto->getDescription()));


            $this->view->redirect("gastos", "listarGastos");
        }
        $this->view->setVariable("gasto", $gasto);
        $this->view->render("gastos", "edit");
    }
    public function delete()
    {
        $gasto = new Gasto();
        $gasto->setId($_REQUEST["id"]);
        $gasto = $this->gastosMapper->findGastosById($gasto);

        if ($gasto->getUsuario() != $this->currentUser) {
            //no puedo por lo que sea
            $this->view->setFlash(sprintf(i18n("Failed to delete \"%s\", do you own the expending?"), $gasto->getDescription()));
            $this->view->redirect("gastos", "listarGastos");
        }
       
        $this->gastosMapper->delete($gasto);
        unlink("uploads/" . $gasto->getUuidFichero());
        $this->view->setFlash(sprintf(i18n("Expending \"%s\" successfully deleted."), $gasto->getDescription()));


        $this->view->redirect("gastos", "listarGastos");
    }

    public function DownloadCSV()
    {
        $gastos = $this->gastosMapper->findGastosByUsername($this->currentUser->getUsername());
        $data = "";

        foreach ($gastos as $gasto) :

            $data = $data . $gasto["descripcion"] . "," . $gasto["cantidad"] . "," . $gasto["fecha"] . "," . $gasto["tipo"] . "\n";

        endforeach;

        $RandomFileId = "Gastos " . $this->currentUser->getUsername() . " " . date("d-m-y");
        $file = fopen("Tmp/$RandomFileId.csv", 'w');

        fwrite($file, $data);

        fclose($file);


        $nombreArchivo = basename("Tmp/$RandomFileId.csv");

        # Algunos encabezados que son justamente los que fuerzan la descarga
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=$nombreArchivo");
        # Leer el archivo y sacarlo al navegador
        readfile("Tmp/$RandomFileId.csv");

        unlink("Tmp/$RandomFileId.csv");

        //$this->view->redirect("gastos", "listarGastos");
    }
}
