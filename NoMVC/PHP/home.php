<?php
//file: personal_area.php

session_start();

// security check ...
if (!isset($_SESSION["currentuser"])) {
    echo "Not in session, this is a restricted area<br>";
    echo "<a href='login.php'>Go to login.php</a>";
    die();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../CSS/home.css">
    <script src="https://code.highcharts.com/highcharts.js"></script>
</head>

<body>
    <div class="header">
        <div class="imgDiv">
            <img class="logoIMG" src="../Imgs/logo.png" alt="">
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
    <?php include("header.php"); ?>
    Hello <?= $_SESSION["currentuser"] ?> welcome to your personal area.<br>
    <div class="mainDiv">

        <div class="charts">
            <div class="chart1" id="chart1">

            </div>
            <div class="chart2" id="chart2">

            </div>
        </div>

    </div>

    <!-- scripts para las graficas -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chart = Highcharts.chart('chart1', {
                chart: {
                    type: 'bar'
                },
                title: {
                    text: 'Fruit Consumption'
                },
                xAxis: {
                    categories: ['Apples', 'Bananas', 'Oranges']
                },
                yAxis: {
                    title: {
                        text: 'Fruit eaten'
                    }
                },
                series: [{
                    name: 'Jane',
                    data: [1, 0, 4]
                }, {
                    name: 'John',
                    data: [5, 7, 3]
                }]
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const chart = Highcharts.chart('chart2', {
                chart: {
                    type: 'bar'
                },
                title: {
                    text: 'Fruit Consumption'
                },
                xAxis: {
                    categories: ['Apples', 'Bananas', 'Oranges']
                },
                yAxis: {
                    title: {
                        text: 'Fruit eaten'
                    }
                },
                series: [{
                    name: 'Jane',
                    data: [1, 0, 4]
                }, {
                    name: 'John',
                    data: [5, 7, 3]
                }]
            });
        });
    </script>

</body>

</html>