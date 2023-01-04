class UserModel extends Fronty.Model {
  constructor(username, passwd, passwdbis, email, lastLogging) {
    super('UserModel');

    this.isLogged = false;

    if (username) {
      this.username = username;
    }

    if (passwd) {
      this.passwd = passwd;
    }

    if (passwdbis) {
      this.passwdbis = passwdbis;
    }

    if (email) {
      this.email = email;
    }

    if (lastLogging) {
      this.lastLogging = lastLogging;
    }
  }

  setUsername(username) {
    this.set((self) => {
      self.username = username;
    });
  }

  setPasswd(passwd) {
    this.set((self) => {
      self.passwd = passwd;
    });
  }

  setPasswdbis(passwdbis) {
    this.set((self) => {
      self.passwdbis = passwdbis;
    });
  }

  setEmail(email) {
    this.set((self) => {
      self.email = email;
    });
  }

  setLastLogging(lastLogging) {
    this.set((self) => {
      self.lastLogging = lastLogging;
    });
  }

  //Acceso al Estado Logeado
  setLoggeduser(loggedUser) {
    this.set((self) => {
      self.currentUser = loggedUser;
      self.isLogged = true;
    });
  }

  logout() {
    this.set((self) => {
      delete self.currentUser;
      self.isLogged = false;
    });
  }
}
