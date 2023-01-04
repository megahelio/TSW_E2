class LoginComponent extends Fronty.ModelComponent {
  constructor(userModel, router) {
    super(Handlebars.templates.login, userModel);
    this.userModel = userModel;
    this.userService = new UserService();
    this.router = router;

    this.addEventListener('click', '#loginbutton', (event) => {
      console.log($('#login').val(), $('#password').val(), CryptoJS.MD5($('#password').val()).toString(), $('#remember').val());
      this.userService.login($('#login').val(), CryptoJS.MD5($('#password').val()).toString(), $('#remember').val())
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

    this.addEventListener('click', '#registerlink', () => {
      this.userModel.set(() => {
        this.userModel.registerMode = true;
      });
    });

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
          alert(I18n.translate('User registered! Please login'));
          this.userModel.set((model) => {
            model.registerErrors = {};
            model.registerMode = false;
          });
        })
        .fail((xhr, errorThrown, statusText) => {
          if (xhr.status == 400) {
            this.userModel.set(() => {
              this.userModel.registerErrors = xhr.responseJSON;
            });
          } else {
            alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
          }
        });
    });
  }
}
