class GastoModel extends Fronty.Model {
    constructor(id, usuario, tipo, cantidad, fecha, descripcion, uuidFichero) {
        super('GastoModel');//call super

        if (id) {
            this.id = id;
        }
        if (usuario) {
            this.usuario = usuario;
        }
        if (tipo) {
            this.tipo = tipo;
        }
        if (cantidad) {
            this.cantidad = cantidad;
        }
        if (fecha) {
            this.fecha = fecha;
        }
        if (descripcion) {
            this.descripcion = descripcion;
        }
        if (uuidFichero) {
            this.uuidFichero = uuidFichero;
        }
    }

    setUsuario(usuario) {
        this.set((self) => {
            self.usuario = usuario;
        });
    }

    setTipo(tipo) {
        this.set((self) => {
            self.tipo = tipo;
        });
    }

    setCantidad(cantidad) {
        this.set((self) => {
            self.cantidad = cantidad;
        });
    }

    setFecha(fecha) {
        this.set((self) => {
            self.fecha = fecha;
        });
    }

    setDescription(descripcion) {
        this.set((self) => {
            self.descripcion = descripcion;
        });
    }

    setUuidFichero(uuidFichero) {
        this.set((self) => {
            self.uuidFichero = uuidFichero;
        });
    }
}