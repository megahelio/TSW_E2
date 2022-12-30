class RegisterComponent extends Fronty.ModelComponent {
    constructor(userModel, router) {
        super(Handlebars.templates.register, userModel);
        this.userModel = userModel;
        this.userService = new UserService();
        this.router = router;
    }
}