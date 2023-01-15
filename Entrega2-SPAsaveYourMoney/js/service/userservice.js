class UserService {
  constructor() {

  }

  loginWithSessionData() {
    var self = this;
    return new Promise((resolve, reject) => {
      if (window.sessionStorage.getItem('login') &&
        window.sessionStorage.getItem('pass')) {
        self.login(window.sessionStorage.getItem('login'), window.sessionStorage.getItem('pass'), false, false)
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

  loginWithCookies() {
    return new Promise((resolve, reject) => {
      if (this.checkCookie("SYM_User") && this.checkCookie("SYM_Pass")) {

        var cookieLogin = this.getCookie("SYM_User");
        var cookiePass = this.getCookie("SYM_Pass");
        $.get({
          url: AppConfig.backendServer + '/rest/user/loginWithRemember',
          beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", "Basic " + btoa(cookieLogin + ":" + cookiePass));
          }
        })
          .then(() => {
            this.setCookie("SYM_User", cookieLogin, 30);
            this.setCookie("SYM_Pass", cookiePass, 30);
            //keep this authentication forever
            window.sessionStorage.setItem('login', cookieLogin);
            window.sessionStorage.setItem('pass', cookiePass);
            $.ajaxSetup({
              beforeSend: (xhr) => {
                xhr.setRequestHeader("Authorization", "Basic " + btoa(cookieLogin + ":" + cookiePass));
              }
            });
            resolve(cookieLogin);
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
        reject(null);
      }
    });
  }

  login(login, pass, remember, doMD5) {
    if (doMD5) {
      var MD5pass = CryptoJS.MD5(pass).toString();
    } else {
      var MD5pass = pass;
    }
    return new Promise((resolve, reject) => {
      if (remember) {

        $.get({
          url: AppConfig.backendServer + '/rest/user/loginWithRemember',
          beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", "Basic " + btoa(login + ":" + MD5pass));
          }
        })
          .then(() => {

            this.setCookie("SYM_User", login, 30);
            this.setCookie("SYM_Pass", MD5pass, 30);
            //keep this authentication forever
            window.sessionStorage.setItem('login', login);
            window.sessionStorage.setItem('pass', MD5pass.toString());
            $.ajaxSetup({
              beforeSend: (xhr) => {
                xhr.setRequestHeader("Authorization", "Basic " + btoa(login + ":" + MD5pass));
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
          url: AppConfig.backendServer + '/rest/user/loginMD5',
          beforeSend: function (xhr) {
            xhr.setRequestHeader("Authorization", "Basic " + btoa(login + ":" + MD5pass));
          }
        })
          .then(() => {
            //keep this authentication forever
            window.sessionStorage.setItem('login', login);
            window.sessionStorage.setItem('pass', MD5pass);
            console.log(login, MD5pass);
            $.ajaxSetup({
              beforeSend: (xhr) => {
                xhr.setRequestHeader("Authorization", "Basic " + btoa(login + ":" + MD5pass));
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

    this.setCookie("SYM_User", "", -1);
    this.setCookie("SYM_Pass", "", -1);

    $.ajaxSetup({
      beforeSend: (xhr) => { }
    });
  }

  deleteSelfAccount() {
    return $.ajax({
      url: AppConfig.backendServer + '/rest/user',
      method: 'DELETE'
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

  updateUser(user) {
    return $.ajax({
      url: AppConfig.backendServer + '/rest/user',
      method: 'PUT',
      data: JSON.stringify(user),
      contentType: 'application/json'
    });
  }
}
