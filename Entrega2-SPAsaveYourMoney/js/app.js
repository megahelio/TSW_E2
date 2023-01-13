/* Main mvcblog-front script */

//load external resources
function loadTextFile(url) {
  return new Promise((resolve, reject) => {
    $.get({
      url: url,
      cache: true,
      beforeSend: function (xhr) {
        xhr.overrideMimeType("text/plain");
      }
    }).then((source) => {
      resolve(source);
    }).fail(() => reject());
  });
}


// Configuration
var AppConfig = {
  backendServer: 'http://localhost/Entrega2-SPAsaveYourMoney'
  //backendServer: '/mvcblog'
}

Handlebars.templates = {};
Promise.all([
  I18n.initializeCurrentLanguage('js/i18n'),
  loadTextFile('templates/components/main.hbs').then((source) =>
    Handlebars.templates.main = Handlebars.compile(source)),
  loadTextFile('templates/components/language.hbs').then((source) =>
    Handlebars.templates.language = Handlebars.compile(source)),
  loadTextFile('templates/components/user.hbs').then((source) =>
    Handlebars.templates.user = Handlebars.compile(source)),
  loadTextFile('templates/components/login.hbs').then((source) =>
    Handlebars.templates.login = Handlebars.compile(source)),
  loadTextFile('templates/components/gasto-edit.hbs').then((source) =>
    Handlebars.templates.gastoedit = Handlebars.compile(source)),
  loadTextFile('templates/components/gasto-row.hbs').then((source) =>
    Handlebars.templates.gastorow = Handlebars.compile(source)),
  loadTextFile('templates/components/gastos-table.hbs').then((source) =>
    Handlebars.templates.gastostable = Handlebars.compile(source)),
  loadTextFile('templates/components/user-edit.hbs').then((source) =>
    Handlebars.templates.useredit = Handlebars.compile(source))
])
  .then(() => {
    $(() => {
      new MainComponent().start();
    });
  }).catch((err) => {
    alert('FATAL: could not start app ' + err);
  });
