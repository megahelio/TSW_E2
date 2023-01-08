class UserEditComponent extends Fronty.ModelComponent {
    constructor(userService, userModel, router) {
        super( Handlebars.templates.useredit, userModel);
        this.userModel = userModel; 
        this.userService = userService; 
        this.addModel('user', userModel);
        this.router = router;

        this.addEventListener('click', '#savebuttonedit', () => {
            this.userModel.passwd = $('#passwd').val();
            this.userModel.passwdbis = $('#passwdbis').val();
            this.userModel.email = $('#email').val();
            this.userService.updateUser(this.userModel)
                .then(() => {
                    window.sessionStorage.setItem('pass',   this.userModel.passwd );
                    this.userService.loginWithSessionData();
                    this.router.goToPage('gastos');
                })
                .fail((xhr, errorThrown, statusText) => {
                    if (xhr.status == 400) {
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
