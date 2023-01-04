class GastosComponent extends Fronty.ModelComponent {
    constructor(gastosModel, userModel, router) {
        super(Handlebars.templates.gastostable, gastosModel, null, null);

        this.gastosModel = gastosModel;
        this.userModel = userModel;
        this.addModel('user', userModel);
        this.router = router;

        this.gastosService = new GastosService();
    }

    onStart() {
        if (!this.userModel.isLogged) {
            this.router.goToPage('login');
        } else {
            this.updateGastos();

            //scripts para las graficas 
            document.addEventListener('DOMContentLoaded', function () {
                const chart = Highcharts.chart('lineGraph', {
                    title: {
                        text: ''
                    },
                    xAxis: {
                        // categories: <?= $lineGraphMonths ?>,//Cambiar por una lista de meses a representar en el grafico
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
                    // series: <?= $lineGraphData ?>//Cambiar por una lista de data a representar en el grafico


                });
            });
            document.addEventListener('DOMContentLoaded', function () {
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
                        // data: [<?= $pieGraphData; ?>] //Cambiar por la data a representar
                    }]
                });
            });
        }
    }

    updateGastos() {
        this.gastosService.findAllGastos().then((data) => {
            console.log(this.gastosModel);
            console.log(data)
            this.gastosModel.setGastos(
                // create a Fronty.Model for each item retrieved from the backend
                data.map(
                    (item) => new GastoModel(item.id, item.usuario, item.tipo, item.cantidad, item.fecha, item.descripcion, item.uuidFichero)
                ))
            console.log(this.gastosModel);
        });
    }

    // Override
    createChildModelComponent(className, element, id, modelItem) {
        return new GastoRowComponent(modelItem, this.userModel, this.router, this);
    }
}

class GastoRowComponent extends Fronty.ModelComponent {
    constructor(gastoModel, userModel, router, gastosComponent) {
        super(Handlebars.templates.gastorow, gastoModel, null, null);

        this.gastosComponent = gastosComponent;

        this.userModel = userModel;
        this.addModel('user', userModel); // a secondary model

        this.router = router;

        this.addEventListener('click', '.remove-button', (event) => {
            if (confirm(I18n.translate('¿Estás seguro?'))) {
                var gastoId = event.target.getAttribute('item');
                this.gastosComponent.gastosService.deleteGasto(gastoId)
                    .fail(() => {
                        alert('gasto cannot be deleted')
                    })
                    .always(() => {
                        this.gastosComponent.updateGastos();
                    });
            }
        });

        this.addEventListener('click', '.edit-button', (event) => {
            var gastoId = event.target.getAttribute('item');
            this.router.goToPage('edit-gasto?id=' + gastoId);
        });
    }
}
