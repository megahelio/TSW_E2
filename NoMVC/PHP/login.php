<?php
//file: login.php

require_once("db_connection.php");
session_start();

if (isset($_POST["username"])) {
    //process login form
    try {
        $stmt = $db->prepare("SELECT count(username) FROM users where username=? and passwd=?");
        $stmt->execute(array($_POST["username"], $_POST["passwd"]));

        if ($stmt->fetchColumn() == 1) {
            // username/password is valid, put the username in _SESSION
            $_SESSION["currentuser"] = $_POST["username"];

            // send user to the restricted area (HTTP 302 code)
            header("Location: ../PHP/home.php");
            die();
        } else {
            echo "Username is not valid<br>";
        }
    } catch (PDOException $ex) {
        die("exception! " . $ex->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/stylesLogin.css">
</head>

<body>
    <div class="row" id="header">
        <div class="logo col-md-6 col-lg-6">
            <img id="logoIMG" src="../Imgs/logo.png" alt="">
        </div>

        <div id="links">
            <!-- Home Redundante? -->
            <ul><a class="link" href="./home.html">Home</a></ul>
            <!-- Para AboutUs y Contact podemos crear secciones en la propio index -->
            <ul><a class="link" href="">About us</a></ul>
            <ul><a class="link" href="">Contact</a></ul>
            <ul><a class="link" href="./login.html">Login</a></ul>
            <ul><a class="link" href="./registro.html">Registro</a></ul>
        </div>
    </div>

    <div class="formulario_login">
        <h1>Inicio de sesion</h1>
        <form method="post">
            <div class="username">
                <input type="text" name="username" onclick="if(this.value=='Usuario') this.value=''" onblur="if(this.value=='') this.value='Usuario'" required>

            </div>
            <div class="username">
                <input type="password" name="passwd" onclick="if(this.value=='Contraseña') this.value=''" onblur="if(this.value=='') this.value='Contraseña'" required>

            </div>
            <div class="recordar2">sd
                <input type="checkbox"> Recordar credenciales
            </div>
            <div class="recordar">Recuperar contraseña</div>
            <input type="submit" value="Iniciar">
            <div class="registrarse">
                <a href="#">No tengo una cuenta.</a>
            </div>
        </form>
    </div>

</body>

</html>
<!-- <html>

<body>
    <h1>Login</h1>
    <form action="login.php" method="POST">
        Username: <input type="text" name="username">
        Password: <input type="password" name="passwd">
        <input type="submit">
    </form>

</body>

</html> -->