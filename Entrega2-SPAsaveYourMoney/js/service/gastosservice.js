class GastosService {
    constructor() {

    }

    findAllGastos() {
        return $.get(AppConfig.backendServer + '/rest/gasto');
    }

    findGasto(id) {
        return $.get(AppConfig.backendServer + '/rest/gasto/' + id);
    }

    deleteGasto(id) {
        return $.ajax({
            url: AppConfig.backendServer + '/rest/gasto/' + id,
            method: 'DELETE'
        });
    }

    saveGasto(gasto) {
        return $.ajax({
            url: AppConfig.backendServer + '/rest/gasto/' + gasto.id,
            method: 'PUT',
            data: JSON.stringify(gasto),
            contentType: 'application/json'
        });
    }

    addGasto(gasto) {
        return $.ajax({
            url: AppConfig.backendServer + '/rest/gasto',
            method: 'POST',
            data: JSON.stringify(gasto),
            contentType: 'application/json'
        });
    }


}
