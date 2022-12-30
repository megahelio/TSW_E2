class PortraitComponent extends Fronty.ModelComponent {
    constructor(userModel) {
        super(Handlebars.templates.portrait, userModel);
        this.userModel = userModel;
        this.userService = new UserService();
    }
}