/**
 * Se contruye con un modelo de gastos y modelo de usuario 
 * para tener los datos de los gastos del usuario y el usuario propio
 * 
 */
class GastosComponent extends Fronty.ModelComponent {
    constructor(gastosModel, userModel, router) {
        //Aquí se vincula al hanblebars compilado en app.js
        super(Handlebars.templates.gastostable, gastosModel, null, null);

        this.gastosModel = gastosModel;
        this.userModel = userModel;
        this.addModel('user', userModel);
        this.router = router;

        this.gastosService = new GastosService();
    }

    onStart() {
        //si no estoy logeado me mando al login
        if (!this.userModel.isLogged) {
            this.router.goToPage('login');
        } else {
            //consulto datos para rellenar la tabla
            this.updateGastos();

            //Creo listener para actualizar las graficas
            this.addEventListener('click', '#daterefresh', () => {
                this.gastosService.findGastosByDate($('#lowdate').val(), $('#highdate').val())
                    .then((data) => {
                        this.drawGraphs(data)
                    });

            });

            console.log("generando graficass");
            //inicializo las graficas con todos los gastos
            this.gastosService.findAllGastos()
                .then((data) => {
                    console.log(data)
                    this.drawGraphs(data)
                });
            //Boton de descargar csv
            this.addEventListener('click', '#downloadcsv', (event) => {
                var downloadButton = document.getElementById("downloadcsv");
                var table = document.getElementById("gastos-table");


                // Convert the table data to a CSV string
                var csv = tableToCSV(table);

                // Create a blob of the CSV data
                var csvData = new File([csv],"table-data.csv", { type: "text/csv" });

                // Create a URL for the CSV file
                var csvUrl = URL.createObjectURL(csvData);

                // Set the href of the download button to the CSV URL
                downloadButton.href = csvUrl;

                // Set the download attribute of the button to a suggested file name
                downloadButton.download = "table-data.csv";
                window.open(downloadButton.href, "_blank");

            });

        }
    }
    /**
     * Recibe la informacion de la base de datos y llama a Highcharts.chart() 
     * @param {*} originalData 
     */
    drawGraphs(originalData) {
        var pieData = getPieGraphDataFormated(originalData)

        //Empieza obtencion de array para el linegraph
        let transformedData = [];//array que contiene los datos para linegraph (series)
        let types = new Set();
        let months = new Set();//set que contiene los meses para linegraph (categories)

        //itero todas las tuplas y genero los sets types y months con todos los meses y tipos que aparecen en el originalData
        for (let expense of originalData) {
            types.add(expense["tipo"]);
            months.add(expense["fecha"].substring(0, 7));
        }

        //para cada tipo
        for (var type of types) {
            //Inicializamos un array con tamaño para guardar un numero por cada mes
            var data = Array.from({ length: months.size }, () => 0);
            //Esto es una aberración computacional
            //recorremos todos los datos por cada tipo que exista y poblamos el array
            for (let expense of originalData) {
                if (expense["tipo"] == type) {
                    let month = expense["fecha"].substring(0, 7);
                    let index = Array.from(months).indexOf(month);
                    data[index] += parseFloat(expense["cantidad"]);
                }
            }
            transformedData.push({ name: type, data });
        }
        //Acaba obtencion de array para el linegraph

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

function tableToCSV(table) {
    var rows = table.rows;
    var csv = "";

    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var cells = row.cells;
        for (var j = 0; j < cells.length-1; j++) {//-1 poque quiero descartar las acciones
            var cell = cells[j];
            var cellText = cell.innerText;
            csv += '"' + cellText + '",';
        }
        csv += "\n";
    }

    return csv;
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

/**
 * Esta clase se usa para generar cada una de las filas de la tabla
 */
class GastoRowComponent extends Fronty.ModelComponent {
    constructor(gastoModel, userModel, router, gastosComponent) {
        super(Handlebars.templates.gastorow, gastoModel, null, null);

        this.gastosComponent = gastosComponent;

        this.userModel = userModel;
        this.addModel('user', userModel); // a secondary model

        this.router = router;

        //Boton de eliminar de la fila
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
        //Boton de editar de la fila
        this.addEventListener('click', '.edit-button', (event) => {
            var gastoId = event.target.getAttribute('item');
            this.router.goToPage('edit-gasto?id=' + gastoId);
        });


    }
}

