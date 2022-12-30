<?php
//file: view/gastos/index.php

require_once(__DIR__."/../../core/ViewManager.php");
$view = ViewManager::getInstance();

$currentuser = $view->getVariable("currentusername");
$graphData = $view->getVariable("graphData");
$initDate = $view->getVariable("initDate");
$finishDate = $view->getVariable("finishDate");
$filtro = $view->getVariable("filtro");
if(!isset($filtro)){
    $filtro = array("SUMINISTRO","OCIO","COMUNICACIONES","COMBUSTIBLE","ALIMENTACION");
}
if(!isset($finishDate) || strlen(trim($finishDate)) == 0){
    $finishDate = date("Y-m-d");
}
if(!isset($initDate) || strlen(trim($initDate)) == 0){
    $date = new DateTime();
    $yearagoDate = $date->modify("-12 months");
    $initDate = $date->format("Y-m-d");
}

$view->setVariable("title", "Gastos");

?>
<div id="opcionesdiv">
	<form id="opciones" method="GET" action="index.php?controller=expenses&amp;action=index" id="opcionesAnalisis" >
		<div id="tituloIntervalo">
			<p><?=i18n("Intervalo de Tiempo")?></p>
		</div>
		<div id="contenedorTiempo">
			<input type="date" name="fechaInicio" id="fechaInicio" class="inputFecha" value = <?=$initDate?>>
			<img id="separadorTiempo" src="img/guion.png" alt="guion">
			<input type="date" name="fechaFin" id="fechaFin" class="inputFecha" value = <?=$finishDate?>>
		</div>
		<div id="tituloCategoria">
			<p><?=i18n("Categoría")?></p>
		</div>
		<div id="contenedorCategoria">
			
			<input type="checkbox" name="filtro" id="filtro" ><label for="filtro" class="filtrar"><?=i18n("Filtrar")?></label>
			<ul>
				<li>
					<input type="checkbox" name="combustible" id="combustible" class="opciones" <?php echo($check = in_array("COMBUSTIBLE",$filtro) ? "checked = 'checked'" : "") ?>><label for="combustible" class="label-opciones"><?=i18n("Combustible")?></label>
				</li>
				<li>
					<input type="checkbox" name="alimentacion" id="alimentacion" class="opciones" <?php echo($check = in_array("ALIMENTACION",$filtro)? "checked = 'checked'" :"")?>><label for="alimentacion" class="label-opciones"><?=i18n("Alimentación")?></label>
				</li>
				<li>
					<input type="checkbox" name="comunicaciones" id="comunicaciones" class="opciones" <?php echo($check = in_array("COMUNICACIONES",$filtro)? "checked = 'checked'" :"")?>><label for="comunicaciones" class="label-opciones"><?=i18n("Comunicaciones")?></label>
				</li>
				<li>
					<input type="checkbox" name="suministro" id="suministro" class="opciones" <?php echo($check = in_array("SUMINISTRO",$filtro)? "checked = 'checked'" :"")?>><label for="suministro" class="label-opciones"><?=i18n("Suministros")?></label>
				</li>
				<li>
					<input type="checkbox" name="ocio" id="ocio" class="opciones" <?php echo($check = in_array("OCIO",$filtro)? "checked = 'checked'" :"")?>><label for="ocio" class="label-opciones"><?=i18n("Ocio")?></label>
				</li>
			</ul>
	
		</div>
        <input type="submit" value="<?=i18n("Actualizar")?>" id="actualizar-graficas"/>
	</form>
</div>

<div id="contentEstructuraAnalisis">
	
	<div id="contenedorGraficaLineas" class="grafica">
		<figure class="highcharts-figure">
			<div id="containerLinea"></div>
			<p class="highcharts-description">
			</p>
		</figure>
	</div>

	<div id="contenedorGraficaTarta" class="grafica">
		<figure class="highcharts-figure">
			<div id="containerTarta"></div>
			<p class="highcharts-description">
			</p>
		</figure>
	</div>
</div>

<?php $view->moveToFragment("css");?>
<link rel="stylesheet" href="css/AnalisisStyle.css" type="text/css">


<?= $view->moveToFragment("javascript") ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<!-- docReady function, which allows us to run javascript when DOM is ready -->
<script src="js/docready.js"></script>

<!-- my JavaScript code, which adds the charts to 'container' using highcharts -->
<script>
	window.docReady(() => {
    createAndAddLineChart();
    createAndAddPieChart();
});

function createAndAddLineChart() {
    <?php 
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $startMonth = explode("-",$initDate)[1];
        $finalMonth = explode("-",$finishDate)[1];
        $startYear = explode("-",$initDate)[0];
        $finalYear = explode("-",$finishDate)[0];
        $numMeses = ($finalYear * 12 + $finalMonth) - ($startYear * 12 + $startMonth) ;
        $mesesIntervalo = array();
        $var = (int) $startMonth -1;
        $cont = $numMeses;
        while($cont >= 0){
            array_push($mesesIntervalo,i18n($meses[$var]));
            $var = ($var + 1);
            if($var == 12) $var = 0;
            $cont = $cont-1;
        }
        $dictMeses = Array(
            "Enero" => "01",
            "Febrero" => "02",
            "Marzo" => "03",
            "Abril" => "04",
            "Mayo" => "05",
            "Junio" => "06",
            "Julio" => "07",
            "Agosto" => "08",
            "Septiembre" => "09",
            "Octubre" => "10",
            "Noviembre" => "11",
            "Diciembre" => "12",
        );

        $alimentacion = Array();
        $combustible = Array();
        $comunicacion = Array();
        $suministro = Array();
        $ocio = Array();

        foreach($mesesIntervalo as $mes){
            if($startMonth > 12){
                $startMonth = 1;
                $startYear = $startYear + 1;
            }
            $sumAlimentacion = 0;
            $sumCombustible = 0;
            $sumComunicacion = 0;
            $sumSuministro = 0;
            $sumOcio = 0;
            foreach($graphData as $index){
                $dateElements = explode("-",$index[1]);
                if($dateElements[0] == $startYear && $dateElements[1] == $dictMeses[$meses[$startMonth-1]]){
                    if($index[0] == "ALIMENTACION"){
                        $sumAlimentacion = $sumAlimentacion + $index[2];
                    }
                    if($index[0] == "COMBUSTIBLE"){
                        $sumCombustible = $sumCombustible + $index[2];
                    }
                    if($index[0] == "COMUNICACIONES"){
                        $sumComunicacion = $sumComunicacion + $index[2];
                    }
                    if($index[0] == "SUMINISTRO"){
                        $sumSuministro = $sumSuministro + $index[2];
                    }
                    if($index[0] == "OCIO"){
                        $sumOcio = $sumOcio + $index[2];
                    }
                }
            }
            array_push($alimentacion,$sumAlimentacion);
            array_push($combustible,$sumCombustible);
            array_push($comunicacion,$sumComunicacion);
            array_push($suministro,$sumSuministro);
            array_push($ocio,$sumOcio);
            $startMonth = $startMonth + 1;
        }

        ?>
        let mesesIntervalo = <?= json_encode($mesesIntervalo) ?>;
    let datos = [{
        name: '<?=i18n("Alimentación")?>',
        data: <?=json_encode($alimentacion)?>
    },{
        name: '<?=i18n("Comunicaciones")?>',
        data: <?=json_encode($comunicacion)?>
    },{
        name: '<?=i18n("Combustible")?>',
        data: <?=json_encode($combustible)?>
    },{
        name: '<?=i18n("Suministros")?>',
        data: <?=json_encode($suministro)?>
    },{
        name: '<?=i18n("Ocio")?>',
        data: <?=json_encode($ocio)?>
    }];

    // Configure and put the chart in the Html document
    Highcharts.chart('containerLinea', {
        title: {
            text: ''
        },


        yAxis: {
            title: {
                text: 'Euros'
            }
        },

        xAxis: {
            accessibility: {
                rangeDescription: 'Range: 2010 to 2020'
            },
            categories: mesesIntervalo

        },

        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },

        plotOptions: {
            series: {
                label: {
                    connectorAllowed: false
                }
            }
        },

        series: datos,

        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }

    });
}

function createAndAddPieChart() {
    Highcharts.chart('containerTarta', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        <?php
            $totalAlimentacion = 0;
            $totalCombustible = 0;
            $totalSuministro = 0;
            $totalComunicaciones = 0;
            $totalOcio = 0;
            foreach($alimentacion as $precio){
                $totalAlimentacion =$totalAlimentacion + $precio;
            }
            foreach($combustible as $precio){
                $totalCombustible =$totalCombustible + $precio;
            }
            foreach($suministro as $precio){
                $totalSuministro =$totalSuministro + $precio;
            }
            foreach($comunicacion as $precio){
                $totalComunicaciones =$totalComunicaciones + $precio;
            }
            foreach($ocio as $precio){
                $totalOcio =$totalOcio + $precio;
            }

            $totalGastos = $totalAlimentacion + $totalCombustible + $totalSuministro + $totalComunicaciones + $totalOcio;
            if($totalGastos == 0) $totalGastos = 1;
        ?>
        series: [{
            name: 'Brands',
            colorByPoint: true,
            data: [{
                name: '<?=i18n("Alimentación")?>',
                y: <?= $totalAlimentacion/$totalGastos ?>,
            },  {
                name: '<?=i18n("Comunicaciones")?>',
                y: <?= $totalComunicaciones/$totalGastos ?>,
            },  {
                name: '<?=i18n("Combustible")?>',
                y: <?= $totalCombustible/$totalGastos ?>,
            }, {
                name: '<?=i18n("Suministros")?>',
                y: <?= $totalSuministro/$totalGastos ?>,
            }, {
                name: '<?=i18n("Ocio")?>',
                y: <?= $totalOcio/$totalGastos ?>
            }]
        }]
    });
}

</script>
