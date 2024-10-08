class GastoAddComponent extends Fronty.ModelComponent {
  constructor(gastosModel, userModel, router) {
    super(Handlebars.templates.gastoedit, gastosModel);
    this.gastosModel = gastosModel; // gastos

    this.userModel = userModel; // global
    this.addModel('user', userModel);
    this.router = router;

    this.gastosService = new GastosService();

    this.addEventListener('click', '#savebutton', () => {
      var newGasto = {};
      newGasto.tipo = $('#tipo').val();
      newGasto.cantidad = $('#cantidad').val();
      newGasto.fecha = $('#fecha').val();
      newGasto.descripcion = $('#descripcion').val();
      newGasto.uuidfichero = $('#uuidFichero').val();
      this.gastosService.addGasto(newGasto)
        .then(() => {
          this.router.goToPage('gastos');
        })
        .fail((xhr, errorThrown, statusText) => {
          if (xhr.status == 400) {
            this.gastosModel.set(() => {
              this.gastosModel.errors = xhr.responseJSON;
            });
          } else {
            alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
          }
        });
    });
  }

  onStart() {
    this.gastosModel.setSelectedGasto(new GastoModel());
  }
}

