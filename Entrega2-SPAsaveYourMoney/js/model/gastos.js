class GastosModel extends Fronty.Model {

    constructor() {
        super('GastosModel'); //call super

        // model attributes
        this.gastos = [];
    }

    setSelectedGasto(gasto) {
        this.set((self) => {
            self.selectedGasto = gasto;
        });
    }

    setGastos(gastos) {
        this.set((self) => {
            self.gastos = gastos;
        });
    }
}
