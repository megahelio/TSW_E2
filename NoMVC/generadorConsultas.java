public class generadorConsultas {
    public static int castRandom() {
        return (int) (Math.random() * 1000);
    }

    public static void main(String args[]) {
        String tabla = "gastos";
        String columnas[] = { "usuario", "tipo", "cantidad", "fecha" };
        String posiblesUsuarios[] = { "andres", "oscar", "señora taboada" };
        String posiblesTipos[] = { "ocio", "comida", "gasolina", "alquiler", "facturas" };

        System.out.print("INSERT INTO `" + tabla + "`(");
        for (int i = 0; i < columnas.length - 1; i++) {
            System.out.print("`" + columnas[i] + "`, ");
        }
        System.out.print("`" + columnas[columnas.length-1] + "`)\nVALUES\n");
        int numeroDeInserts = (args.length == 0 ? 10 : Integer.parseInt(args[0]));
        for (int i = 0; i < numeroDeInserts - 1; i++) {
            int randomUsuario = castRandom() % 3;
            int randomTipo = castRandom() % 5;
            int randomCantidad = castRandom() % 200+1;
            int randomAnho = (castRandom() % 22) + 2000;
            int randomMes = (castRandom() % 12) + 1;
            int randomDia = (castRandom() % 31) + 1;
            System.out.println("(\'" + posiblesUsuarios[randomUsuario] + "\', \'" + posiblesTipos[randomTipo] +
                    "\', \'" + randomCantidad + "\', \'" + randomAnho + "-" + randomMes + "-" + randomDia + "\'),");
        }
        int randomUsuario = castRandom() % 3;
        int randomTipo = castRandom() % 5;
        int randomCantidad = castRandom() % 200;
        int randomAnho = (castRandom() % 22) + 2000;
        int randomMes = (castRandom() % 12) + 1;
        int randomDia = (castRandom() % 31) + 1;
        System.out.println("(\'" + posiblesUsuarios[randomUsuario] + "\', \'" + posiblesTipos[randomTipo] +
                "\', \'" + randomCantidad + "\', \'" + randomAnho + "-" + randomMes + "-" + randomDia + "\');");

    }
}

// INSERT
// INTO`gastos`(`id`,`usuario`,`tipo`,`cantidad`,`fecha`,`descripcion`,`fichero`)

// VALUES
// (1, 'andres', 'comida', '3', '2010-11-01 00:00:00', 'bocaudillos', 'nohay'),
// (2, 'oscar', 'ocio', '3', '2013-10-02 00:00:00', 'Cine', NULL);