<?php
require_once(__DIR__ . "/../../core/ViewManager.php");
$view = ViewManager::getInstance();
$errors = $view->getVariable("errors");

$lineGraphData = $view->getVariable("lineGraphData");
$lineGraphMonths = $view->getVariable("lineGraphMonths");

$pieGraphData = $view->getVariable("pieGraphData");
$view->setVariable("title", i18n("Home"));
print_r($_SESSION);
?>

<div class="mainDiv">
    <div class="rango">
        <p><?= i18n("Date range")?></p>
    </div>
    <form method="post" action="index.php?controller=gastos&amp;action=index">
        <div>
            <div class="tamanhoI">
                <p><?= i18n("From")?></p>
                <input type="date" name="initDate" class="dateI" placeholder="<?= i18n("First Month") ?>">
            </div>
            <div>
                <div class="tamanhoF">
                    <p><?= i18n("to")?></p>
                    <input type="date" name="endDate" class="dateF" placeholder="<?= i18n("Last Month") ?>">
                </div>
                <?= isset($errors["graphsForm"]) ? i18n($errors["graphsForm"]) : "" ?><br>
                <input type="submit" value="<?= i18n("update")?>">

            </div>

            <div class="charts">
                <div id="lineGraph"></div>
                <div id="pieGraph"></div>
            </div>


            <!-- scripts para las graficas -->

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const chart = Highcharts.chart('lineGraph', {
                        title: {
                            text: ''
                        },
                        xAxis: {
                            categories: <?= $lineGraphMonths ?>,
                            title: {
                                text: 'Meses'
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Euros'
                            }
                        },
                        plotOptions: {
                            series: {
                                label: {
                                    connectorAllowed: false
                                }
                            }
                        },
                        series: <?= $lineGraphData ?>


                    });
                });

                document.addEventListener('DOMContentLoaded', function() {
                    const chart = Highcharts.chart('pieGraph', {
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
                        series: [{
                            name: 'gastos',
                            colorByPoint: true,
                            data: [<?= $pieGraphData; ?>]
                        }]
                    });
                });
            </script>


            <?php $view->moveToFragment("css"); ?>
            <link rel="stylesheet" type="text/css" href="./view/CSS/home.css">
            <?php $view->moveToDefaultFragment(); ?>