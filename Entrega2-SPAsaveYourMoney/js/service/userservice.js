class UserService {
  constructor() {

  }

  loginWithSessionData() {
    var self = this;
    return new Promise((resolve, reject) => {
      if (window.sessionStorage.getItem('login') &&
        window.sessionStorage.getItem('pass')) {
        self.login(window.sessionStorage.getItem('login'), window.sessionStorage.getItem('pass'), false)
          .then(() => {
            resolve(window.sessionStorage.getItem('login'));
          })
          .catch(() => {
            reject();
          });
      } else {
        resolve(null);
      }
    });
  }

  login(login, pass, remember) {
    return new Promise((resolve, reject) => {
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
            resolve();
          })
          .fail((error) => {
            window.sessionStorage.removeItem('login');
            window.sessionStorage.removeItem('pass');
            $.ajaxSetup({
              beforeSend: (xhr) => { }
            });
            reject(error);
          });



      } else {

        if ($('#remember').is(':checked')) {

          $.get({
            url: AppConfig.backendServer + '/rest/user/loginWithRemember',
            beforeSend: function (xhr) {
              xhr.setRequestHeader("Authorization", "Basic " + btoa(login + ":" + pass));
            }
          })
            .then(() => {

              this.setCookie("SYM_User", login, 30);
              this.setCookie("SYM_Pass", CryptoJS.MD5(pass), 30);
              //keep this authentication forever
              window.sessionStorage.setItem('login', login);
              window.sessionStorage.setItem('pass', pass);
              $.ajaxSetup({
                beforeSend: (xhr) => {
                  xhr.setRequestHeader("Authorization", "Basic " + btoa(login + ":" + pass));
                }
              });
              resolve();
            })
            .fail((error) => {
              window.sessionStorage.removeItem('login');
              window.sessionStorage.removeItem('pass');
              $.ajaxSetup({
                beforeSend: (xhr) => { }
              });
              reject(error);
            });



        } else {

          $.get({
            url: AppConfig.backendServer + '/rest/user/login',
            beforeSend: function (xhr) {
              xhr.setRequestHeader("Authorization", "Basic " + btoa(login + ":" + pass));
            }
          })
            .then(() => {
              //keep this authentication forever
              window.sessionStorage.setItem('login', login);
              window.sessionStorage.setItem('pass', pass);
              $.ajaxSetup({
                beforeSend: (xhr) => {
                  xhr.setRequestHeader("Authorization", "Basic " + btoa(login + ":" + pass));
                }
              });
              resolve();
            })
            .fail((error) => {
              window.sessionStorage.removeItem('login');
              window.sessionStorage.removeItem('pass');
              $.ajaxSetup({
                beforeSend: (xhr) => { }
              });
              reject(error);
            });

        }

      }
    });

  }
  setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }

  getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }

  checkCookie(cname) {
    let cvalue = this.getCookie(cname);
    if (cvalue != "") {
      return true;
    } else {
      return false;
    }
  }


  logout() {
    window.sessionStorage.removeItem('login');
    window.sessionStorage.removeItem('pass');
    $.ajaxSetup({
      beforeSend: (xhr) => { }
    });
  }

  register(user) {
    return $.ajax({
      url: AppConfig.backendServer + '/rest/user',
      method: 'POST',
      data: JSON.stringify(user),
      contentType: 'application/json'
    });
  }
}
