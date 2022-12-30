class MainComponent extends Fronty.RouterComponent {
    constructor() {

        super('frontyapp', Handlebars.templates.main, 'maincontent');

        // models instantiation
        // we can instantiate models at any place
        this.userModel = new UserModel();
        this.expensesModel = new ExpensesModel();
        this.userService = new UserService();

        super.setRouterConfig({
            portrait: {
                component: new PortraitComponent(this.userModel),
                tittle: 'Portrait'
            },
            expenses: {
                component: new ExpensesComponent(this.expensesModel, this.userModel, 'maincontent'),
                title: 'Expenses'
            },
            // 'edit-expense': {
            //     component: new ExpenseEditComponent(this.expensesModel, this.userModel, this),
            //     title: 'Edit Expense'
            // },
            // 'add-expense': {
            //     component: new ExpenseAddComponent(this.expensesModel, this.userModel, this),
            //     title: 'Add Expense'
            // },
            login: {
                component: new LoginComponent(this.userModel, this),
                title: 'Login'
            },
            register: {
                component: new RegisterComponent(this.userModel, this),
                tittle: 'Register'
            },
            // graphs: {
            //     component: new GraphsComponent(this.expensesModel, this.userModel, this),
            //     tittle: 'Graphs'
            // },
            
            defaultRoute: 'Expenses'
        });
        
        Handlebars.registerHelper('currentPage', () => {
            return super.getCurrentPage();
        });
        this.addChildComponent(this._createLanguageComponent());
        this.addChildComponent(this._createPortraitComponent());
    }

    start() {
        // override the start() function in order to first check if there is a logged user
        // in sessionStorage, so we try to do a relogin and start the main component
        // only when login is checked
        console.log("aaaaaaaaaaaaaaaaaa");
        console.log(this.userModel);
        this.userService.loginWithSessionData()
        .then((logged) => {
            if (logged != null) {
                this.userModel.setLoggeduser(logged);
            }
            super.start(); // now we can call start
        });
        console.log(this.userModel);
    }

    _createLanguageComponent() {
        console.log("testin");
        var languageComponent = new Fronty.ModelComponent(Handlebars.templates.language, this.userModel, this.routerModel, 'languagecontrol');
        // language change links
        languageComponent.addEventListener('click', '#englishlink', () => {
            I18n.changeLanguage('default');
            document.location.reload();
            console.log(userModel);
        });

        languageComponent.addEventListener('click', '#spanishlink', () => {
            I18n.changeLanguage('es');
            document.location.reload();
        });

        return languageComponent;
    }

    _createPortraitComponent() {
        var portraitComponent = new Fronty.ModelComponent(Handlebars.templates.portrait, this.routerModel, 'maincontent');
        return portraitComponent;
    }
}