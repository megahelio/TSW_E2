class GastoEditComponent extends Fronty.ModelComponent {
    constructor(gastosModel, userModel, router) {
        super(Handlebars.templates.gastoedit, gastosModel);
        this.gastosModel = gastosModel; // gastos
        this.userModel = userModel; // global
        this.addModel('user', userModel);
        this.router = router;

        this.gastosService = new GastosService();

        this.addEventListener('click', '#savebutton', () => {
            this.gastosModel.selectedGasto.title = $('#title').val();
            this.gastosModel.selectedGasto.content = $('#content').val();
            this.gastosService.saveGasto(this.gastosModel.selectedGasto)
                .then(() => {
                    this.gastosModel.set((model) => {
                        model.errors = []
                    });
                    this.router.goToPage('gastos');
                })
                .fail((xhr, errorThrown, statusText) => {
                    if (xhr.status == 400) {
                        this.gastosModel.set((model) => {
                            model.errors = xhr.responseJSON;
                        });
                    } else {
                        alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
                    }
                });

        });
    }

    onStart() {
        var selectedId = this.router.getRouteQueryParam('id');
        if (selectedId != null) {
            this.gastosService.findGasto(selectedId)
                .then((gasto) => {
                    this.gastosModel.setSelectedGasto(gasto);
                });
        }
    }
}