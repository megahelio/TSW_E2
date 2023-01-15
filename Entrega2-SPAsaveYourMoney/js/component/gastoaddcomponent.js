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




      var file = document.getElementById("uuidFichero").files[0];
      var formData = new FormData();
      formData.append("file", file);


      var xhr = new XMLHttpRequest();
      xhr.open("POST", "http://localhost/Entrega2-SPAsaveYourMoney/rest/file/111", true);
      xhr.onload = function () {
        if (xhr.status === 200) {
          console.log("File uploaded successfully");
        } else {
          console.error("Error uploading file");
        }
      };
      xhr.send(file);


      /*fetch("http://localhost/Entrega2-SPAsaveYourMoney/rest/file/1", {
        method: "POST",
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          console.log("Success:", data);
        })
        .catch(error => {
          console.error("Error:", error);
        });*/

      //console.log(xhr.responseText);
      newGasto.uuidfichero = dsads1;
      this.gastosService.addGasto(newGasto)
        .then(() => {
          //this.router.goToPage('gastos');
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

