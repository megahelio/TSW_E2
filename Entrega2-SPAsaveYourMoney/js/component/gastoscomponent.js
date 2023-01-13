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

            this.addEventListener('click', '#daterefresh', () => {
                this.gastosService.findGastosByDate($('#lowdate').val(), $('#highdate').val())
                    .then((data) => {
                        this.drawGraphs(data)
                    });

            });

            console.log("generando graficass");

            this.gastosService.findAllGastos()
                .then((data) => {
                    console.log(data)
                    this.drawGraphs(data)
                });

        }
    }
    drawGraphs(originalData) {
        var pieData = getPieGraphDataFormated(originalData)

        let transformedData = [];
        let types = new Set();
        let months = new Set();

        for (let expense of originalData) {
            types.add(expense["tipo"]);
            months.add(expense["fecha"].substring(0, 7));
        }

        for (var type of types) {
            var data = Array.from({ length: months.size }, () => 0);
            for (let expense of originalData) {
                if (expense["tipo"] == type) {
                    let month = expense["fecha"].substring(0, 7);
                    let index = Array.from(months).indexOf(month);
                    data[index] += parseFloat(expense["cantidad"]);
                }
            }
            transformedData.push({ name: type, data });
        }

        Highcharts.chart('pieGraph', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Porcentaje de cada gasto'
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
                data: pieData

            }]
        });
        console.log(pieData)
        Highcharts.chart('lineGraph', {
            title: {
                text: ''
            },
            xAxis: {
                categories: Array.from(months),
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
            series: transformedData


        });

    }
    updateGastos() {
        this.gastosService.findAllGastos().then((data) => {
            this.gastosModel.setGastos(
                // create a Fronty.Model for each item retrieved from the backend
                data.map(
                    (item) => new GastoModel(item.id, item.usuario, item.tipo, item.cantidad, item.fecha, item.descripcion, item.uuidFichero)
                ))
        });
    }

    // Override
    createChildModelComponent(className, element, id, modelItem) {
        return new GastoRowComponent(modelItem, this.userModel, this.router, this);
    }


}



function getPieGraphDataFormated(gastosData) {
    //calcular el total
    let totalEachArray = new Map();
    let fractionEach = new Map();
    let total = 0;
    let i = 0;
    let tipos = Object.keys(gastosData);

    for (item of gastosData) {

        var set = false;

        if (totalEachArray.has(item.tipo)) {
            totalEachArray.set(item.tipo, parseFloat(totalEachArray.get(item.tipo)) + parseFloat(item.cantidad));
            set = true;
        }


        if (set == false) {
            totalEachArray.set(item.tipo, parseFloat(item.cantidad));
        }

        total += parseFloat(item.cantidad);

    }

    if (total == 0) total = 1;

    var keys = totalEachArray.keys();

    for (key of keys) {
        console.log(key);
        fractionEach.set(key, totalEachArray.get(key) / total);
    }

    let data = [];
    keys = totalEachArray.keys();

    for (key of keys) {
        data.push({ name: key, y: fractionEach.get(key) })
    }

    return data;
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

