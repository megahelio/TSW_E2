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

            //console.log("generando graficasss");

            

            console.log("generando graficass");
            var gastos = this.gastosService.findAllGastos();
            gastos.then((data) => {
                //console.log(data);
                console.log(getPieGraph(data));


                var pieGraph = Highcharts.chart('pieGraph', {
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
                        data: [{
                            name: 'Chrome',
                            y: 70.67,
                            sliced: true,
                            selected: true
                        }, {
                            name: 'Edge',
                            y: 14.77
                        }, {
                            name: 'Firefox',
                            y: 4.86
                        }, {
                            name: 'Safari',
                            y: 2.63
                        }, {
                            name: 'Internet Explorer',
                            y: 1.53
                        }, {
                            name: 'Opera',
                            y: 1.40
                        }, {
                            name: 'Sogou Explorer',
                            y: 0.84
                        }, {
                            name: 'QQ',
                            y: 0.51
                        }, {
                            name: 'Other',
                            y: 2.6
                        }]

                    }]
                });
            })

            //scripts para las graficas 
            var lineGraph = Highcharts.chart('lineGraph', {
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


            })


            //getPieGraph(this.gastosService.findAllGastos());


        }
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

function getPieGraph(gastosData) {
    //calcular el total
    let totalEachArray = new Map();
    let fractionEach = new Map();
    let total = 0;
    let i = 0;
    let tipos = Object.keys(gastosData);

    //console.log(gastosData);

    for (item of gastosData) {
        //item.tipo;
        //item.cantidad;

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

    //console.log(totalEachArray);
    //console.log(total);

    if (total == 0) total = 1;

    var keys = totalEachArray.keys();

    for (key of keys) {
        console.log(key);
        fractionEach.set(key, totalEachArray.get(key) / total);
    }

    //console.log(fractionEach);
    let data = "";
    keys = totalEachArray.keys();

    for (key of keys) {
        data += "{\n";
        data += "name:" + key + ",\n";
        data += "y:" + fractionEach.get(key) + "\n";
        data += "},";
    }

    data = data.substring(0, data.length - 1);
    //console.log(data);

    return data;


    //crear el string con la data

    /*let j = 0;
    while (j < tipos.length) {
        data += "{\n";
        data += `name: '${i18n(tipos[j])}',\n`;
        data += `y: ${dataFraction[tipos[j]]}\n`;
        if (tipos[j] == tipoMayor) {
            data += ",\n";
            data += "sliced: true,\n selected: true\n";
        }
        data += "}";

        if (j != tipos.length - 1) {
            data += ",";
        }
        j++;
    }

    return data;*/

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

