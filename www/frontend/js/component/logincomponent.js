class LoginComponent extends Fronty.ModelComponent {
    constructor(userModel, router) {
        super(Handlebars.templates.login, userModel);
        this.userModel = userModel;
        this.userService = new UserService();
        this.router = router;

        this.addEventListener('click', '#boton-inicio', (event) => {
            this.userService.login($('#username').val(), $('#password').val())
                .then(() => {
                    this.router.goToPage('expenses');
                    this.userModel.setLoggeduser($('#username').val());
                })
                .catch((error) => {
                    router.isLogged = false;
                    this.userModel.set((model) => {
                        model.loginError = error.responseText;
                    });
                    this.userModel.logout();
                });
        });
    }
}