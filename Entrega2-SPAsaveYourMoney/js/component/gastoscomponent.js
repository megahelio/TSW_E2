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
        this.updateGastos();
    }

    updateGastos() {
        this.gastosService.findAllGastos().then((data) => {
            console(data.id)
            this.gastosModel.setGastos(
                // create a Fronty.Model for each item retrieved from the backend
                data.map(
                    (item) => new GastoModel(item.id, item.usuario, item.tipo, item.cantidad, item.fecha, item.description, item.uuidFichero)
                ));
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
            this.router.goToPage('editar-gasto?id=' + gastoId);
        });
    }
}
