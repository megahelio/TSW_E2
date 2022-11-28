<!-- php defines variable, verificas registerOK, Creas peticion post (POST,Redirect,GET), autologin? -->
<?php
//file: register.php

require_once("db_connection.php");
session_start();

$errors = array(); // validation errors
$registerOK = false; // was the register ok?

if (isset($_POST["username"])) {
    //process register form

    // validate fields length
    $validationOK = true;
    if (strlen($_POST["username"]) < 5) {
        $errors["username"] = "Username must be at least 5 characters length";
        $validationOK = false;
    }
    if (strlen($_POST["passwd"]) < 5) {
        $errors["passwd"] = "Password must be at least 5 characters length";
        $validationOK = false;
    }

    // validate if user exists...
    if ($validationOK) {
        try {

            $stmt = $db->prepare("SELECT count(username) FROM users where username=?");
            $stmt->execute(array($_POST["username"]));

            if ($stmt->fetchColumn() > 0) {
                // username already exists!
                $errors["username"] = "Username already exists";
                $validationOK = false;
            }
        } catch (PDOException $ex) {
            die("exception! " . $ex->getMessage());
        }
    }

    if ($validationOK) {
        // validation all OK, now insert...
        try {

            $stmt = $db->prepare("INSERT INTO users values (?,?,?)");
            $stmt->execute(array($_POST["username"], $_POST["email"], $_POST["passwd"]));

            $registerOK = true;
        } catch (PDOException $ex) {
            die("exception! " . $ex->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/regstyles.css">
</head>

<body>
    <?php if ($registerOK) {
        header("Location: ../PHP/home.php");
    } ?>


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

    <div class="formulario_registro">
        <h1>Unete a nosotros</h1>
        <form method="post">
            <div class="campoformulario">
                <input type="text" name="username" value="<?= isset($_POST["username"]) ? $_POST["username"] : "" ?>">
                <?= isset($errors["username"]) ? $errors["username"] : "" ?>
                <label>Nombre de Usuario</label>
            </div>

            <div class="campoformulario">
                <input type="text" name="email" value="<?= isset($_POST["email"]) ? $_POST["email"] : "" ?>">
                <?= isset($errors["email"]) ? $errors["email"] : "" ?>
                <label>Correo electronico</label>
            </div>

            <div class="campoformulario">
                <input type="password" name="passwd" value="<?= isset($_POST["passwd"]) ? $_POST["passwd"] : "" ?>">
                <?= isset($errors["passwd"]) ? $errors["passwd"] : "" ?>
                <label>Contrasena</label>
            </div>

            <div class="campoformulario">
                <input type="password" required>
                <label>Repite la contrasena</label>
            </div>

            <div class="botonregistrarse">
                <input type="submit" value="Empieza a ahorrar">
            </div>
        </form>

    </div>

</body>

</html>