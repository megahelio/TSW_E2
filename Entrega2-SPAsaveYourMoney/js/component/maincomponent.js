class MainComponent extends Fronty.RouterComponent {
  constructor() {
    super('frontyapp', Handlebars.templates.main, 'maincontent');

    // models instantiation
    // we can instantiate models at any place
    this.userModel = new UserModel();
    this.postsModel = new PostsModel();
    this.userService = new UserService();

    this.gastosModel = new GastosModel();

    super.setRouterConfig({
      gastos: {
        component: new GastosComponent(this.gastosModel, this.userModel, this),
        title: 'Gastos'
      },
      'edit-gasto': {
        component: new GastoEditComponent(this.gastosModel, this.userModel, this),
        title: 'Edit Gasto'
      },
      'add-gasto': {
        component: new GastoAddComponent(this.gastosModel, this.userModel, this),
        title: 'Add Gasto'
      },
      posts: {
        component: new PostsComponent(this.postsModel, this.userModel, this),
        title: 'Posts'
      },
      'view-post': {
        component: new PostViewComponent(this.postsModel, this.userModel, this),
        title: 'Post'
      },
      'edit-post': {
        component: new PostEditComponent(this.postsModel, this.userModel, this),
        title: 'Edit Post'
      },
      'add-post': {
        component: new PostAddComponent(this.postsModel, this.userModel, this),
        title: 'Add Post'
      },
      login: {
        component: new LoginComponent(this.userModel, this),
        title: 'Login'
      },
      defaultRoute: 'gastos'
    });

    Handlebars.registerHelper('currentPage', () => {
      return super.getCurrentPage();
    });

    this.addChildComponent(this._createUserBarComponent());
    this.addChildComponent(this._createLanguageComponent());

  }

  start() {
    // override the start() function in order to first check if there is a logged user
    // in sessionStorage, so we try to do a relogin and start the main component
    // only when login is checked
    this.userService.loginWithSessionData()
      .then((logged) => {
        if (logged != null) {
          this.userModel.setLoggeduser(logged);
        }
        super.start(); // now we can call start
      })
      .fail((error) => {

        if (this.checkCookie("SYM_User") && this.checkCookie("SYM_Pass")) {

          cookieLogin = this.getCookie("SYM_User");
          cookiePass = this.getCookie("SYM_Pass");
          $.get({
            url: AppConfig.backendServer + '/rest/user/loginMD5',
            beforeSend: function (xhr) {
              xhr.setRequestHeader("Authorization", "Basic " + btoa(cookieLogin + ":" + cookiePass));
            }
          })
            .then(() => {
              //keep this authentication forever
              window.sessionStorage.setItem('login', cookieLogin);
              window.sessionStorage.setItem('pass', cookiePass);
              $.ajaxSetup({
                beforeSend: (xhr) => {
                  xhr.setRequestHeader("Authorization", "Basic " + btoa(cookieLogin + ":" + cookiePass));
                }
              });
            })
            .fail((error) => {
              window.sessionStorage.removeItem('login');
              window.sessionStorage.removeItem('pass');
              $.ajaxSetup({
                beforeSend: (xhr) => { }
              });
            });
          this.start();
        }
      });



  }

  _createUserBarComponent() {
    var userbar = new Fronty.ModelComponent(Handlebars.templates.user, this.userModel, 'userbar');

    userbar.addEventListener('click', '#logoutbutton', () => {
      this.userModel.logout();
      this.userService.logout();
      super.goToPage('login');
    });

    return userbar;
  }

  _createLanguageComponent() {
    var languageComponent = new Fronty.ModelComponent(Handlebars.templates.language, this.routerModel, 'languagecontrol');
    // language change links
    languageComponent.addEventListener('click', '#englishlink', () => {
      I18n.changeLanguage('default');
      document.location.reload();
    });

    languageComponent.addEventListener('click', '#spanishlink', () => {
      I18n.changeLanguage('es');
      document.location.reload();
    });

    return languageComponent;
  }
}
