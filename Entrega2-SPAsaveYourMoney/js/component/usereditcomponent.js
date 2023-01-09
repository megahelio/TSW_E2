class UserEditComponent extends Fronty.ModelComponent {
    constructor(userService, userModel, router) {
        super(Handlebars.templates.useredit, userModel);
        this.userModel = userModel;
        this.userService = userService;
        this.addModel('user', userModel);
        this.router = router;

        //Al clicar en "editar usuario"
        this.addEventListener('click', '#savebuttonedit', () => {
            //generamos una nueva instancia del modelo de usuario en el que guardaremos los nuevos datos del usuario
            this.toEditUserModer = new UserModel();
            this.toEditUserModer.passwd = $('#passwd').val();
            this.toEditUserModer.passwdbis = $('#passwdbis').val();
            this.toEditUserModer.email = $('#email').val();
            //Mandamos actualizar el usuario (updateUser return new Promise)
            this.userService.updateUser(this.toEditUserModer)
                //Si se completa con exito
                .then(() => {
                    //si no cambian la contraseña entonces directamente lo mando a la pagina de gastos
                    if (this.toEditUserModer.passwd == "" && this.toEditUserModer.passwdbis == "") {
                        this.router.goToPage('gastos');
                    } else {
                        //si se cambia la contraseña
                        //Haremos login con la nueva contraseña para actualizar los valores de usuario y contraseña "window.sessionStorage" y las cookies de recuerdo si es el caso.
                        this.userService.login(this.userModel.currentUser, this.toEditUserModer.passwd, this.userService.checkCookie("SYM_User") && this.userService.checkCookie("SYM_Pass"), true)
                            .then(() => {
                                //la redireccion a gastos debe ir dentro de la resolución de la promesa, sino gastoscomponent llamará a findAllGastos() con las credenciales antiguas. (asincronismo)
                                this.router.goToPage('gastos');
                            })

                    }
                })
                //si hay algún fallo
                .fail((xhr, errorThrown, statusText) => {
                    //"Enseñamos" el error que devuelve el back
                    if (xhr.status == 400) {
                        //asignamos a la variable de errores que lee HandleBars los errores que devuelve el back
                        this.userModel.set((model) => {
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
            this.userService.findUser(selectedId)
                .then((gasto) => {
                    this.userModel(this.userModel);
                });
        }
    }
}
