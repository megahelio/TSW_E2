class LoginComponent extends Fronty.ModelComponent {
  constructor(userModel, router) {
    super(Handlebars.templates.login, userModel);
    this.userModel = userModel;
    this.userService = new UserService();
    this.router = router;

    //boton de login
    this.addEventListener('click', '#loginbutton', (event) => {
      this.userService.login($('#login').val(), $('#password').val(), $('#remember').prop('checked'), true)
        .then(() => {
          this.router.goToPage('gastos');
          this.userModel.setLoggeduser($('#login').val());
        })
        .catch((error) => {
          this.userModel.set((model) => {
            model.loginError = error.responseText;
          });
          this.userModel.logout();
        });
    });

    // cambiar visibilidad del formulario de registro
    this.addEventListener('click', '#registerlink', () => {
      this.userModel.set(() => {
        this.userModel.registerMode = true;
      });
    });

    //boton de registro
    this.addEventListener('click', '#registerbutton', () => {
      this.userService.register({
        // username: $('#registerusername').val(),
        // password: $('#registerpassword').val()
        username: $('#registerusername').val(),
        passwd: $('#registerpassword').val(),
        passwdbis: $('#registerpasswdbis').val(),
        email: $('#registeremail').val()
      })
        .then(() => {
          this.userService.login($('#registerusername').val(), $('#registerpassword').val(), false, true)
            .then(() => {
              this.userModel.setLoggeduser($('#registerusername').val());
              this.userModel.set((model) => {
                model.registerErrors = {};
                model.registerMode = false;
              })
              this.router.goToPage('gastos');
            })
        })
        .fail((xhr, errorThrown, statusText) => {
          if (xhr.status == 400) {
            this.userModel.set(() => {
              this.userModel.registerErrors = xhr.responseJSON;
            });
          } else {
            alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
          }
        })
    });
  }
  onStart() {
    if (this.userModel.isLogged) {
      this.router.goToPage('gastos');
    }
  }
}
